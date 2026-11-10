<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Study_Load;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\AcademicYear;
use App\Models\Workload;
use App\Models\Course;
use App\Models\Room;
use App\Models\Section;
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
    public const TOTAL_MAX_HOURS = 30;
    public const EVENING_MAX_HOURS = 30;
    public const PART_TIME_MAX_HOURS = 30;
    private const FULL_TIME_MAX_HOURS = 30;

    /** Fixed caps use the upper end of each policy range. */
    public const SPECIAL_POSITION_MAX_HOURS = [
        'Vice-President' => 3,
        'University Director' => 6,
        'Campus Director' => 6,
        'Assistant Campus Director' => 9,
        'Dean of Instruction' => 9,
        'College Dean' => 9,
        'Associate College Dean' => 12,
        'Department SUC Function Chairperson' => 16,
        'PERG Chair' => 16,
        'Campus Secretary' => 16,
        'College Secretary' => 16,
    ];

    public static function facultyMaxHours(Faculty $faculty, string $shift = 'day'): float
    {
        if ($shift === 'evening') {
            return (float) self::EVENING_MAX_HOURS;
        }

        if ($faculty->fac_employment_type === 'part_time') {
            return (float) self::PART_TIME_MAX_HOURS;
        }

        if (
            $faculty->fac_special_position
            && isset(self::SPECIAL_POSITION_MAX_HOURS[$faculty->fac_special_position])
        ) {
            return (float) self::SPECIAL_POSITION_MAX_HOURS[$faculty->fac_special_position];
        }

        return (float) self::FULL_TIME_MAX_HOURS;
    }

    public static function workloadShiftForAssignment(
        bool $sectionIsEvening,
        string $scheduleShift,
        ?string $existingShift = null,
        bool $hasScheduledMeetings = false
    ): string {
        if ($sectionIsEvening) {
            return 'evening';
        }

        if ($hasScheduledMeetings && in_array($existingShift, ['day', 'evening'], true)) {
            return $existingShift;
        }

        return $scheduleShift;
    }

    /** Assign one course/session to several weekdays as one all-or-nothing action. */
    public function assignMultiple(array $data): array
    {
        $days = array_values(array_unique($data['days'] ?? []));
        if (!$days) {
            return ['success' => false, 'conflict' => false, 'message' => 'Choose at least one day.'];
        }

        try {
            return DB::transaction(function () use ($data, $days) {
                $created = [];
                foreach ($days as $day) {
                    $dayData = $data;
                    unset($dayData['days']);
                    $dayData['day'] = $day;
                    // The workload is unchanged between days in this one
                    // request, so recalculate it once after all meetings save.
                    $result = $this->assign($dayData, syncWorkload: false, refreshResponse: false);
                    if (empty($result['success'])) {
                        throw new RuntimeException('__MULTI_DAY_FAIL__' . json_encode($result));
                    }
                    $created[] = $result['schedule'];
                }

                $this->syncWorkload($data['fac_id'], $data['sem_id']);

                return [
                    'success' => true,
                    'message' => 'Schedule added for ' . implode(', ', $days) . '.',
                    'schedules' => $created,
                ];
            });
        } catch (RuntimeException $e) {
            if (str_starts_with($e->getMessage(), '__MULTI_DAY_FAIL__')) {
                return json_decode(substr($e->getMessage(), strlen('__MULTI_DAY_FAIL__')), true)
                    ?: ['success' => false, 'conflict' => false, 'message' => 'Unable to add the selected days.'];
            }
            throw $e;
        }
    }

    public function assign(array $data, bool $syncWorkload = true, bool $refreshResponse = true): array
    {
        if (!$this->facultyAccountIsActive($data['fac_id'])) {
            return ['success' => false, 'conflict' => false, 'message' => 'This faculty account is pending approval or inactive and cannot be assigned a schedule.'];
        }

        $course = Course::findOrFail($data['subj_id']);
        $isRec = $course->course_code === 'REC';

        if ($conflict = $this->findConflict($data, $isRec)) {
            return ['success' => false, 'conflict' => true, 'message' => $conflict];
        }

        try {
            $result = DB::transaction(function () use ($data, $syncWorkload) {
                $course = Course::findOrFail($data['subj_id']);
                $isRec = $course->course_code === 'REC';
                $faculty = $this->lockConflictResources($data, $isRec);

                if ($conflict = $this->findConflict($data, $isRec)) {
                    throw new RuntimeException('SCHEDULE_CONFLICT:' . $conflict);
                }

                $loadQuery = Study_Load::where([
                    'sl_fac_id' => $data['fac_id'],
                    'sl_course_id' => $data['subj_id'],
                    'sl_sem_id' => $data['sem_id'],
                ]);
                if (!$isRec) {
                    $loadQuery->where('sl_sec_id', $data['sec_id']);
                }
                $studyLoad = $loadQuery->withExists('schedules')->first();
                $courseHours = self::courseHours($course);
                if (!$isRec) {
                    $this->assertCourseHoursAvailable($data, $course);
                }

                $scheduleShift = $data['shift'] ?? 'day';
                $sectionIsEvening = !empty($data['sec_id'])
                    && (bool) Section::whereKey($data['sec_id'])->value('sec_is_evening');
                $workloadShift = self::workloadShiftForAssignment(
                    $sectionIsEvening,
                    $scheduleShift,
                    $studyLoad?->sl_shift,
                    (bool) ($studyLoad?->schedules_exists ?? false)
                );
                $currentHours = $this->facultyHoursForShift($data['fac_id'], $data['sem_id'], $workloadShift);
                $additionalHours = (!$studyLoad || $studyLoad->sl_shift !== $workloadShift) ? $courseHours : 0;
                $hoursAfterAssignment = $currentHours + $additionalHours;
                $maxHours = self::facultyMaxHours($faculty, $workloadShift);
                if ($hoursAfterAssignment > $maxHours) {
                    throw new RuntimeException("LOAD_LIMIT:Cannot add {$course->course_code}. {$faculty->fac_first_name} {$faculty->fac_last_name} has {$currentHours} teaching hours in the {$workloadShift} workload; this course adds {$additionalHours} hours, totaling {$hoursAfterAssignment} against the {$maxHours}-hour limit.");
                }

                if (!$studyLoad) {
                    $studyLoad = Study_Load::create([
                        'sl_fac_id' => $data['fac_id'],
                        'sl_course_id' => $data['subj_id'],
                        'sl_shift' => $workloadShift,
                        'sl_sec_id' => $data['sec_id'] ?? null,
                        'sl_sem_id' => $data['sem_id'],
                        'sl_assigned_by' => Auth::id(),
                        'sl_status'      => 'approved',
                        'sl_assigned_at' => now(),
                    ]);
                } elseif ($studyLoad->sl_shift !== $workloadShift) {
                    $studyLoad->sl_shift = $workloadShift;
                    $studyLoad->save();
                }

                $scheduleData = [
                    'sch_load_id'    => $studyLoad->sl_id,
                    'sch_fac_id'     => $data['fac_id'],
                    'sch_course_id'  => $data['subj_id'],
                    'sch_sec_id'     => $data['sec_id'],
                    'sch_room_id'    => $data['room_id'],
                    'sch_sem_id'     => $data['sem_id'],
                    'sch_day'        => $data['day'],
                    'sch_shift'      => $scheduleShift,
                    'sch_start_time' => $data['start_time'],
                    'sch_end_time'   => $data['end_time'],
                    'sch_status'     => 'draft',
                    'sch_is_active'  => true,
                    'sch_created_by' => Auth::id(),
                ];
                $schedule = Schedule::create($scheduleData);

                $updatedHours = $syncWorkload
                    ? $this->syncWorkload($data['fac_id'], $data['sem_id'])
                    : null;

                return [
                    'schedule' => $schedule,
                    'hours' => $updatedHours,
                    'max_hours' => $maxHours,
                    'hours_after' => $hoursAfterAssignment,
                    'workload_shift' => $workloadShift,
                ];
            });
            $schedule = $result['schedule'];
            $notice = '';
            if ($syncWorkload && $result['hours_after'] !== null) {
                $hours = $result['hours_after'];
                $max = $result['max_hours'];
                $shift = $result['workload_shift'];
                $notice = $hours >= $max
                    ? " Faculty load is now {$hours} of {$max} teaching hours in the {$shift} workload; further course assignments in this shift are blocked."
                    : '';
            }
            if ($refreshResponse) {
                $schedule->load(['subject', 'faculty', 'section', 'room']);
            }
            return [
                'success' => true,
                'message' => 'Schedule added.' . $notice,
                'schedule' => $schedule,
                'hours_after' => $result['hours_after'],
            ];
        } catch (RuntimeException $e) {
            if (str_starts_with($e->getMessage(), 'SCHEDULE_CONFLICT:')) {
                return ['success' => false, 'conflict' => true, 'message' => substr($e->getMessage(), strlen('SCHEDULE_CONFLICT:'))];
            }
            if (str_starts_with($e->getMessage(), 'LOAD_LIMIT:')) {
                return ['success' => false, 'conflict' => false, 'message' => substr($e->getMessage(), 11)];
            }
            if (str_starts_with($e->getMessage(), 'COURSE_HOURS:')) {
                return ['success' => false, 'conflict' => false, 'message' => substr($e->getMessage(), 13)];
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

        $course = Course::findOrFail($data['subj_id']);
        $isRec = $course->course_code === 'REC';
        if ($conflict = $this->findConflict($data, $isRec, $scheduleId)) {
            return ['success' => false, 'conflict' => true, 'message' => $conflict];
        }

        $oldFacultyId = null;
        $oldSemesterId = null;
        try {
            $schedule = DB::transaction(function () use ($scheduleId, $data, $isRec, &$oldFacultyId, &$oldSemesterId) {
                $faculty = $this->lockConflictResources($data, $isRec);
                $schedule = Schedule::whereKey($scheduleId)->lockForUpdate()->firstOrFail();
                $oldFacultyId = $schedule->sch_fac_id;
                $oldSemesterId = $schedule->sch_sem_id;

                if ($conflict = $this->findConflict($data, $isRec, $scheduleId)) {
                    throw new RuntimeException('SCHEDULE_CONFLICT:' . $conflict);
                }

                $course = Course::findOrFail($data['subj_id']);
                $courseHours = self::courseHours($course);
                $this->assertCourseHoursAvailable($data, $course, $scheduleId);
                $scheduleShift = $data['shift'] ?? $schedule->sch_shift ?? 'day';
                $sectionIsEvening = !empty($data['sec_id'])
                    && (bool) Section::whereKey($data['sec_id'])->value('sec_is_evening');

                $sourceLoad = Study_Load::whereKey($schedule->sch_load_id)
                    ->lockForUpdate()
                    ->first();
                $isSameFacultySectionChange = $sourceLoad
                    && $schedule->sch_fac_id === $data['fac_id']
                    && $schedule->sch_course_id === $data['subj_id']
                    && $schedule->sch_sem_id === $data['sem_id']
                    && $schedule->sch_sec_id !== $data['sec_id'];
                $sourceLoadHasOtherSchedules = $isSameFacultySectionChange
                    && Schedule::where('sch_load_id', $sourceLoad->sl_id)
                        ->where('sch_id', '!=', $schedule->sch_id)
                        ->exists();

                $targetLoad = Study_Load::where([
                    'sl_fac_id' => $data['fac_id'],
                    'sl_course_id' => $data['subj_id'],
                    'sl_sec_id' => $data['sec_id'],
                    'sl_sem_id' => $data['sem_id'],
                ])->withExists('schedules')->first();
                $workloadShift = self::workloadShiftForAssignment(
                    $sectionIsEvening,
                    $scheduleShift,
                    $targetLoad?->sl_shift,
                    (bool) ($targetLoad?->schedules_exists ?? false)
                );
                if ($isSameFacultySectionChange) {
                    $workloadShift = $sectionIsEvening ? 'evening' : 'day';
                }
                $isSameLoad = $targetLoad && $targetLoad->sl_id === $schedule->sch_load_id;
                $targetLoadNeedsShiftMove = $targetLoad && $targetLoad->sl_shift !== $workloadShift;
                $sourceLoadToDelete = null;
                if ($isSameFacultySectionChange && $sourceLoadHasOtherSchedules) {
                    $sourceWorkloadShift = (bool) Section::whereKey($sourceLoad->sl_sec_id)->value('sec_is_evening')
                        ? 'evening'
                        : ($sourceLoad->sl_shift ?? 'day');
                    if ($sourceLoad->sl_shift !== $sourceWorkloadShift) {
                        $sourceLoad->sl_shift = $sourceWorkloadShift;
                        $sourceLoad->save();
                    }
                    $schedule->sch_load_id = $sourceLoad->sl_id;
                } elseif ($isSameFacultySectionChange) {
                    $sourceWorkloadShift = (bool) Section::whereKey($sourceLoad->sl_sec_id)->value('sec_is_evening')
                        ? 'evening'
                        : ($sourceLoad->sl_shift ?? 'day');
                    if ($sourceWorkloadShift !== $workloadShift && !$targetLoad) {
                        $currentHours = $this->facultyHoursForShift($data['fac_id'], $data['sem_id'], $workloadShift);
                        $projectedHours = $currentHours + $courseHours;
                        $maxHours = self::facultyMaxHours($faculty, $workloadShift);
                        if ($projectedHours > $maxHours) {
                            throw new RuntimeException("LOAD_LIMIT:Cannot move {$course->course_code}. {$faculty->fac_first_name} {$faculty->fac_last_name} has {$currentHours} teaching hours in the {$workloadShift} workload; this course adds {$courseHours} hours, totaling {$projectedHours} against the {$maxHours}-hour limit.");
                        }
                    }

                    if ($targetLoad && $targetLoad->sl_id !== $sourceLoad->sl_id) {
                        $schedule->sch_load_id = $targetLoad->sl_id;
                        $sourceLoadToDelete = $sourceLoad->sl_id;
                    } else {
                        $sourceLoad->sl_sec_id = $data['sec_id'];
                        $sourceLoad->sl_shift = $workloadShift;
                        $sourceLoad->save();
                        $schedule->sch_load_id = $sourceLoad->sl_id;
                    }
                } elseif (!$isSameLoad || $targetLoadNeedsShiftMove) {
                    $currentHours = $this->facultyHoursForShift($data['fac_id'], $data['sem_id'], $workloadShift);
                    $replacingSameFacultyLoad = $schedule->sch_fac_id === $data['fac_id'] && $schedule->sch_sem_id === $data['sem_id'];
                    $sourceLoad = Study_Load::with('section')->find($schedule->sch_load_id);
                    $sourceWorkloadShift = $sourceLoad?->section?->sec_is_evening
                        ? 'evening'
                        : ($sourceLoad?->sl_shift ?? 'day');
                    $oldLoadHasOtherSchedules = Schedule::where('sch_load_id', $schedule->sch_load_id)
                        ->where('sch_id', '!=', $schedule->sch_id)
                        ->exists();
                    $willRemoveOldLoad = !$oldLoadHasOtherSchedules;
                    if ($replacingSameFacultyLoad && $willRemoveOldLoad && $sourceWorkloadShift === $workloadShift) {
                        $oldCourse = Course::find($schedule->sch_course_id);
                        $currentHours -= $oldCourse ? self::courseHours($oldCourse) : 0;
                    }
                    $hoursToAdd = (!$targetLoad || $targetLoadNeedsShiftMove) ? $courseHours : 0;
                    $projectedHours = $currentHours + $hoursToAdd;
                    $maxHours = self::facultyMaxHours($faculty, $workloadShift);
                    if ($projectedHours > $maxHours) {
                        throw new RuntimeException("LOAD_LIMIT:Cannot assign {$course->course_code}. {$faculty->fac_first_name} {$faculty->fac_last_name} has {$currentHours} teaching hours in the {$workloadShift} workload; this course adds {$hoursToAdd} hours, totaling {$projectedHours} against the {$maxHours}-hour limit.");
                    }
                    if (!$targetLoad) {
                        $targetLoad = Study_Load::create([
                            'sl_fac_id' => $data['fac_id'],
                            'sl_course_id' => $data['subj_id'],
                            'sl_shift' => $workloadShift,
                            'sl_sec_id' => $data['sec_id'],
                            'sl_sem_id' => $data['sem_id'],
                            'sl_assigned_by' => Auth::id(),
                            'sl_assigned_at' => now(),
                            'sl_status' => 'approved',
                        ]);
                    } elseif ($targetLoadNeedsShiftMove) {
                        $targetLoad->sl_shift = $workloadShift;
                        $targetLoad->save();
                    }
                    if (!$isSameLoad) {
                        $oldLoadId = $schedule->sch_load_id;
                        $schedule->sch_load_id = $targetLoad->sl_id;
                        $schedule->save();
                        if ($oldLoadId !== $targetLoad->sl_id) {
                            Study_Load::whereKey($oldLoadId)->whereDoesntHave('schedules')->delete();
                        }
                    }
                }
                $scheduleData = [
                    'sch_fac_id' => $data['fac_id'],
                    'sch_course_id' => $data['subj_id'],
                    'sch_sec_id' => $data['sec_id'],
                    'sch_room_id' => $data['room_id'],
                    'sch_sem_id' => $data['sem_id'],
                    'sch_day' => $data['day'],
                    'sch_shift' => $scheduleShift,
                    'sch_start_time' => $data['start_time'],
                    'sch_end_time' => $data['end_time'],
                ];
                $schedule->update($scheduleData);
                if ($sourceLoadToDelete) {
                    Study_Load::whereKey($sourceLoadToDelete)
                        ->whereDoesntHave('schedules')
                        ->delete();
                }
                $this->syncWorkload($data['fac_id'], $data['sem_id']);
                return $schedule;
            });
        } catch (RuntimeException $e) {
            if (str_starts_with($e->getMessage(), 'SCHEDULE_CONFLICT:')) {
                return ['success' => false, 'conflict' => true, 'message' => substr($e->getMessage(), strlen('SCHEDULE_CONFLICT:'))];
            }
            if (str_starts_with($e->getMessage(), 'LOAD_LIMIT:')) return ['success' => false, 'conflict' => false, 'message' => substr($e->getMessage(), 11)];
            if (str_starts_with($e->getMessage(), 'COURSE_HOURS:')) return ['success' => false, 'conflict' => false, 'message' => substr($e->getMessage(), 13)];
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

    public function facultyHours(string $facultyId, string $semesterId): float
    {
        $hours = DB::table('study_load as sl')
            ->join('course as c', 'c.course_id', '=', 'sl.sl_course_id')
            ->where('sl.sl_fac_id', $facultyId)
            ->where('sl.sl_sem_id', $semesterId)
            ->sum(DB::raw('COALESCE(c.course_lecture_hours, 0) + COALESCE(c.course_lab_hours, 0)'));

        return (float) $hours;
    }

    public function facultyHoursForShift(string $facultyId, string $semesterId, string $shift): float
    {
        return (float) Study_Load::query()
            ->where('sl_fac_id', $facultyId)
            ->where('sl_sem_id', $semesterId)
            ->where('sl_shift', $shift)
            ->with('subject')
            ->get()
            ->sum(fn($load) => self::courseHours($load->subject));
    }

    public static function courseHours(?Course $course): float
    {
        return $course
            ? (float) ($course->course_lecture_hours ?? 0) + (float) ($course->course_lab_hours ?? 0)
            : 0.0;
    }

    private function facultyAccountIsActive(string $facultyId): bool
    {
        return Faculty::query()
            ->whereKey($facultyId)
            ->whereHas('user', fn($query) => $query->where('usr_is_active', true))
            ->exists();
    }

    private function assertCourseHoursAvailable(
        array $data,
        Course $course,
        ?string $excludeScheduleId = null
    ): void {
        $allowedHours = self::courseHours($course);
        if ($allowedHours <= 0) {
            return;
        }

        $query = Schedule::query()
            ->where('sch_course_id', $data['subj_id'])
            ->where('sch_sec_id', $data['sec_id'])
            ->where('sch_sem_id', $data['sem_id'])
            ->where('sch_is_active', true);
        if ($excludeScheduleId) {
            $query->where('sch_id', '!=', $excludeScheduleId);
        }

        $scheduledHours = (float) $query->get(['sch_start_time', 'sch_end_time'])
            ->sum(function ($schedule) {
                $start = \Illuminate\Support\Carbon::parse($schedule->sch_start_time);
                $end = \Illuminate\Support\Carbon::parse($schedule->sch_end_time);
                return $start->diffInMinutes($end) / 60;
            });
        $newHours = \Illuminate\Support\Carbon::parse($data['start_time'])
            ->diffInMinutes(\Illuminate\Support\Carbon::parse($data['end_time'])) / 60;
        $projectedHours = $scheduledHours + $newHours;

        if ($projectedHours > $allowedHours) {
            $section = \App\Models\Section::find($data['sec_id']);
            $sectionName = $section?->sec_name ?? 'the selected section';
            throw new RuntimeException(
                "COURSE_HOURS:Overextension: {$course->course_code} for {$sectionName} allows {$allowedHours} total hours per week. "
                    . "Already scheduled: {$scheduledHours} hours; this meeting adds {$newHours}, totaling {$projectedHours} hours."
            );
        }
    }

    public function syncWorkload(string $facultyId, string $semesterId, ?float $knownTotalHours = null): float
    {
        $semester = Semester::find($semesterId);
        $workload = Workload::firstOrNew(['wl_fac_id' => $facultyId, 'wl_sem_id' => $semesterId]);
        if (!$workload->exists) $workload->wl_id = (string) \Illuminate\Support\Str::uuid();
        $workload->wl_ay_id = $semester?->sem_ay_id ?? AcademicYear::where('ay_is_active', true)->value('ay_id');
        $totalHours = $knownTotalHours ?? $this->facultyHours($facultyId, $semesterId);
        $workload->wl_total_hours = $totalHours;
        $workload->wl_type = $workload->wl_type ?? 'regular';
        $workload->save();
        return $totalHours;
    }

    /**
     * Checks faculty / room / section double-booking: same day, same
     * semester, overlapping time range. Returns a human-readable conflict
     * message, or null when the slot is free.
     */
    private function lockConflictResources(array $data, bool $isRec): Faculty
    {
        $faculty = Faculty::whereKey($data['fac_id'])->lockForUpdate()->firstOrFail();

        if (!$isRec && !empty($data['room_id'])) {
            Room::whereKey($data['room_id'])->lockForUpdate()->firstOrFail();
        }

        if (!$isRec && !empty($data['sec_id'])) {
            Section::whereKey($data['sec_id'])->lockForUpdate()->firstOrFail();
        }

        return $faculty;
    }

    private function findConflict(array $data, bool $isRec = false, ?string $excludeScheduleId = null): ?string
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

        $facultyClash = (clone $base)->where('sch_fac_id', $data['fac_id'])
            ->with(['subject', 'section', 'faculty'])
            ->first();
        if ($facultyClash) {
            $professor = $this->professorName($facultyClash);
            $section = $this->sectionName($facultyClash);
            return "Professor {$professor} already has {$this->codeOf($facultyClash)} with {$section} on {$data['day']} ({$facultyClash->sch_start_time}–{$facultyClash->sch_end_time}), which overlaps this time.";
        }

        if (!$isRec && !empty($data['room_id'])) {
            $roomClash = (clone $base)->where('sch_room_id', $data['room_id'])
                ->with(['subject', 'section', 'faculty', 'room'])
                ->first();
            if ($roomClash) {
                $room = $roomClash->room->room_name ?? 'the selected room';
                return "Room {$room} is already used by {$this->professorName($roomClash)} for {$this->codeOf($roomClash)} with {$this->sectionName($roomClash)} on {$data['day']} ({$roomClash->sch_start_time}–{$roomClash->sch_end_time}).";
            }
        }

        if (!$isRec && !empty($data['sec_id'])) {
            $sectionClash = (clone $base)->where('sch_sec_id', $data['sec_id'])
                ->with(['subject', 'section', 'faculty'])
                ->first();
            if ($sectionClash) {
                $professor = $this->professorName($sectionClash);
                $section = $this->sectionName($sectionClash);
                return "Section {$section} already has {$this->codeOf($sectionClash)} with {$professor} on {$data['day']} ({$sectionClash->sch_start_time}–{$sectionClash->sch_end_time}).";
            }
        }

        return null;
    }

    private function codeOf(Schedule $schedule): string
    {
        return $schedule->subject->subj_code ?? 'another class';
    }

    private function sectionName(Schedule $schedule): string
    {
        return $schedule->section->sec_name ?? 'an unnamed section';
    }

    private function professorName(Schedule $schedule): string
    {
        $faculty = $schedule->faculty;
        return $faculty
            ? trim(($faculty->fac_first_name ?? '') . ' ' . ($faculty->fac_last_name ?? ''))
            : 'the selected professor';
    }
}
