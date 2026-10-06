<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Study_Load;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\AcademicYear;
use App\Models\Workload;
use App\Models\Course;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Single place where PBS and PBT both create/update/delete schedule entries.
 * Neither controller talks to the `schedule` table directly — this keeps the
 * conflict rules (faculty / room / section double-booking) defined exactly
 * once, so a class added from either page is checked the same way.
 *
 * Every method returns an array shaped like:
 *   ['success' => true,  'schedule' => Schedule]
 *   ['success' => false, 'conflict' => true,  'message' => '...']   <- don't save, show as a toast
 *   ['success' => false, 'conflict' => false, 'message' => '...']   <- some other failure
 */
class ScheduleAssignmentService
{
    private const FULL_TIME_MAX_UNITS = 30;
    private const PART_TIME_MAX_UNITS = 22;

    public function assign(array $data): array
    {
        if (!$this->facultyAccountIsActive($data['fac_id'])) {
            return ['success' => false, 'conflict' => false, 'message' => 'This faculty account is pending approval or inactive and cannot be assigned a schedule.'];
        }

        if ($conflict = $this->findConflict($data)) {
            return ['success' => false, 'conflict' => true, 'message' => $conflict];
        }

        try {
            $schedule = DB::transaction(function () use ($data) {
                $faculty = Faculty::whereKey($data['fac_id'])->lockForUpdate()->firstOrFail();
                // One study load can have several meeting sessions (for example,
                // the same course/section on different weekdays).
                $loadQuery = Study_Load::where([
                    'sl_fac_id' => $data['fac_id'], 'sl_course_id' => $data['subj_id'],
                    'sl_sec_id' => $data['sec_id'], 'sl_sem_id' => $data['sem_id'],
                ]);
                $studyLoad = $loadQuery->first();
                $currentUnits = $this->facultyUnits($data['fac_id'], $data['sem_id']);
                $maxUnits = $faculty->fac_employment_type === 'part_time'
                    ? self::PART_TIME_MAX_UNITS
                    : self::FULL_TIME_MAX_UNITS;
                if ($studyLoad && $currentUnits > $maxUnits) {
                    throw new RuntimeException("LOAD_LIMIT:Cannot schedule this course. {$faculty->fac_first_name} {$faculty->fac_last_name} is already over the {$maxUnits}u limit at {$currentUnits}u.");
                }

                if (!$studyLoad) {
                    $course = Course::findOrFail($data['subj_id']);
                    $courseUnits = (float) $course->course_lecture_hours + (float) $course->course_lab_hours;
                    if ($currentUnits + $courseUnits > $maxUnits) {
                        throw new RuntimeException("LOAD_LIMIT:Cannot add {$course->course_code}. {$faculty->fac_first_name} {$faculty->fac_last_name} currently has {$currentUnits}u; this course adds {$courseUnits}u and would exceed the {$maxUnits}u limit.");
                    }

                    $studyLoad = Study_Load::create([
                        'sl_fac_id' => $data['fac_id'],
                        'sl_course_id' => $data['subj_id'],
                        'sl_sec_id' => $data['sec_id'],
                        'sl_sem_id' => $data['sem_id'],
                        'sl_assigned_by' => Auth::id(),
                        'sl_status'      => 'approved',
                        'sl_assigned_at' => now(),
                    ]);
                }

                $schedule = Schedule::create([
                    'sch_load_id'    => $studyLoad->sl_id,
                    'sch_fac_id'     => $data['fac_id'],
                    'sch_course_id'  => $data['subj_id'],
                    'sch_sec_id'     => $data['sec_id'],
                    'sch_room_id'    => $data['room_id'],
                    'sch_sem_id'     => $data['sem_id'],
                    'sch_day'        => $data['day'],
                    'sch_start_time' => $data['start_time'],
                    'sch_end_time'   => $data['end_time'],
                    'sch_status'     => 'draft',
                    'sch_is_active'  => true,
                    'sch_created_by' => Auth::id(),
                ]);

                $this->syncWorkload($data['fac_id'], $data['sem_id']);
                return $schedule;
            });
            $units = $this->facultyUnits($data['fac_id'], $data['sem_id']);
            $max = Faculty::find($data['fac_id'])?->fac_employment_type === 'part_time'
                ? self::PART_TIME_MAX_UNITS : self::FULL_TIME_MAX_UNITS;
            $notice = $units >= $max ? " Faculty load is now {$units}u of {$max}u; further course assignments are blocked." : '';
            return ['success' => true, 'message' => 'Schedule added.' . $notice, 'schedule' => $schedule->load(['subject', 'faculty', 'section', 'room'])];
        } catch (RuntimeException $e) {
            if (str_starts_with($e->getMessage(), 'LOAD_LIMIT:')) {
                return ['success' => false, 'conflict' => false, 'message' => substr($e->getMessage(), 11)];
            }
            throw $e;
        } catch (QueryException $e) {
            if (($e->errorInfo[0] ?? $e->getCode()) === '23505') {
                return ['success' => false, 'conflict' => true, 'message' => 'Conflict: that schedule already exists.'];
            }
            throw $e;
        }
    }

    public function update(string $scheduleId, array $data): array
    {
        if (!$this->facultyAccountIsActive($data['fac_id'])) {
            return ['success' => false, 'conflict' => false, 'message' => 'This faculty account is pending approval or inactive and cannot be assigned a schedule.'];
        }

        $schedule = Schedule::findOrFail($scheduleId);

        if ($conflict = $this->findConflict($data, excludeScheduleId: $scheduleId)) {
            return ['success' => false, 'conflict' => true, 'message' => $conflict];
        }

        $oldFacultyId = $schedule->sch_fac_id;
        $oldSemesterId = $schedule->sch_sem_id;
        try {
            $schedule = DB::transaction(function () use ($schedule, $data) {
                $faculty = Faculty::whereKey($data['fac_id'])->lockForUpdate()->firstOrFail();
                $targetLoad = Study_Load::where([
                    'sl_fac_id' => $data['fac_id'], 'sl_course_id' => $data['subj_id'],
                    'sl_sec_id' => $data['sec_id'], 'sl_sem_id' => $data['sem_id'],
                ])->first();
                if (!$targetLoad || $targetLoad->sl_id !== $schedule->sch_load_id) {
                    $course = Course::findOrFail($data['subj_id']);
                    $courseUnits = (float) $course->course_lecture_hours + (float) $course->course_lab_hours;
                    $currentUnits = $this->facultyUnits($data['fac_id'], $data['sem_id']);
                    $replacingSameFacultyLoad = $schedule->sch_fac_id === $data['fac_id'] && $schedule->sch_sem_id === $data['sem_id'];
                    $oldLoadHasOtherSchedules = Schedule::where('sch_load_id', $schedule->sch_load_id)
                        ->where('sch_id', '!=', $schedule->sch_id)
                        ->exists();
                    $willRemoveOldLoad = !$oldLoadHasOtherSchedules;
                    if ($replacingSameFacultyLoad && $willRemoveOldLoad) {
                        $oldCourse = Course::find($schedule->sch_course_id);
                        $currentUnits -= (float) ($oldCourse?->course_lecture_hours ?? 0)
                            + (float) ($oldCourse?->course_lab_hours ?? 0);
                    }
                    $projectedUnits = $currentUnits + ($targetLoad ? 0 : $courseUnits);
                    $unitsToAdd = $targetLoad ? 0 : $courseUnits;
                    $maxUnits = $faculty->fac_employment_type === 'part_time' ? self::PART_TIME_MAX_UNITS : self::FULL_TIME_MAX_UNITS;
                    if ($projectedUnits > $maxUnits) {
                        throw new RuntimeException("LOAD_LIMIT:Cannot assign {$course->course_code}. {$faculty->fac_first_name} {$faculty->fac_last_name} currently has {$currentUnits}u; this course adds {$unitsToAdd}u and would exceed the {$maxUnits}u limit.");
                    }
                    $targetLoad ??= Study_Load::create([
                        'sl_fac_id' => $data['fac_id'], 'sl_course_id' => $data['subj_id'],
                        'sl_sec_id' => $data['sec_id'], 'sl_sem_id' => $data['sem_id'],
                        'sl_assigned_by' => Auth::id(), 'sl_assigned_at' => now(), 'sl_status' => 'approved',
                    ]);
                    $oldLoadId = $schedule->sch_load_id;
                    $schedule->sch_load_id = $targetLoad->sl_id;
                    $schedule->save();
                    if ($oldLoadId !== $targetLoad->sl_id) {
                        Study_Load::whereKey($oldLoadId)->whereDoesntHave('schedules')->delete();
                    }
                }
                $schedule->update([
                    'sch_fac_id' => $data['fac_id'], 'sch_course_id' => $data['subj_id'],
                    'sch_sec_id' => $data['sec_id'], 'sch_room_id' => $data['room_id'],
                    'sch_sem_id' => $data['sem_id'], 'sch_day' => $data['day'],
                    'sch_start_time' => $data['start_time'], 'sch_end_time' => $data['end_time'],
                ]);
                $this->syncWorkload($data['fac_id'], $data['sem_id']);
                return $schedule;
            });
        } catch (RuntimeException $e) {
            if (str_starts_with($e->getMessage(), 'LOAD_LIMIT:')) return ['success' => false, 'conflict' => false, 'message' => substr($e->getMessage(), 11)];
            throw $e;
        }
        if ($oldFacultyId !== $data['fac_id'] || $oldSemesterId !== $data['sem_id']) $this->syncWorkload($oldFacultyId, $oldSemesterId);
        return ['success' => true, 'schedule' => $schedule->load(['subject', 'faculty', 'section', 'room'])];
    }

    public function delete(string $scheduleId): array
    {
        $schedule = Schedule::findOrFail($scheduleId);
        $loadId = $schedule->sch_load_id;
        $facultyId = $schedule->sch_fac_id;
        $semesterId = $schedule->sch_sem_id;
        $schedule->delete();

        // Free up the study load so the same faculty/subject/section/semester
        // combo can be rescheduled later without hitting the unique constraint.
        Study_Load::whereKey($loadId)->whereDoesntHave('schedules')->delete();
        $this->syncWorkload($facultyId, $semesterId);

        return ['success' => true, 'message' => 'Schedule deleted.'];
    }

    public function facultyUnits(string $facultyId, string $semesterId): float
    {
        return (float) Study_Load::where('sl_fac_id', $facultyId)
            ->where('sl_sem_id', $semesterId)
            ->with('subject')
            ->get()
            ->sum(fn ($load) => (float) ($load->subject?->course_lecture_hours ?? 0) + (float) ($load->subject?->course_lab_hours ?? 0));
    }

    private function facultyAccountIsActive(string $facultyId): bool
    {
        return Faculty::query()
            ->whereKey($facultyId)
            ->whereHas('user', fn ($query) => $query->where('usr_is_active', true))
            ->exists();
    }

    public function syncWorkload(string $facultyId, string $semesterId): void
    {
        $semester = Semester::find($semesterId);
        $workload = Workload::firstOrNew(['wl_fac_id' => $facultyId, 'wl_sem_id' => $semesterId]);
        if (!$workload->exists) $workload->wl_id = (string) \Illuminate\Support\Str::uuid();
        $workload->wl_ay_id = $semester?->sem_ay_id ?? AcademicYear::where('ay_is_active', true)->value('ay_id');
        $workload->wl_total_hours = $this->facultyUnits($facultyId, $semesterId);
        $workload->wl_type = $workload->wl_type ?? 'regular';
        $workload->save();
    }

    /**
     * Checks faculty / room / section double-booking: same day, same
     * semester, overlapping time range. Returns a human-readable conflict
     * message, or null when the slot is free.
     */
    private function findConflict(array $data, ?string $excludeScheduleId = null): ?string
    {
        $base = Schedule::query()
            ->where('sch_sem_id', $data['sem_id'])
            ->where('sch_day', $data['day'])
            ->where('sch_is_active', true)
            ->where('sch_start_time', '<', $data['end_time'])
            ->where('sch_end_time', '>', $data['start_time']);

        if ($excludeScheduleId) {
            $base->where('sch_id', '!=', $excludeScheduleId);
        }

        $facultyClash = (clone $base)->where('sch_fac_id', $data['fac_id'])->with('subject')->first();
        if ($facultyClash) {
            return "Conflict: this teacher already has {$this->codeOf($facultyClash)} at an overlapping time on {$data['day']}.";
        }

        $roomClash = (clone $base)->where('sch_room_id', $data['room_id'])->with('subject')->first();
        if ($roomClash) {
            return "Conflict: this room is already booked for {$this->codeOf($roomClash)} at an overlapping time on {$data['day']}.";
        }

        $sectionClash = (clone $base)->where('sch_sec_id', $data['sec_id'])->with('subject')->first();
        if ($sectionClash) {
            return "Conflict: this section already has {$this->codeOf($sectionClash)} at an overlapping time on {$data['day']}.";
        }

        return null;
    }

    private function codeOf(Schedule $schedule): string
    {
        return $schedule->subject->subj_code ?? 'another class';
    }
}
