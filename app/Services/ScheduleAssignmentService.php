<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Study_Load;
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
    public function assign(array $data): array
    {
        if ($conflict = $this->findConflict($data)) {
            return ['success' => false, 'conflict' => true, 'message' => $conflict];
        }

        try {
            return DB::transaction(function () use ($data) {
                // schedule.sch_load_id is UNIQUE — a (faculty, subject, section,
                // semester) combination can only carry ONE meeting time in this
                // schema. We reuse the study_load row if it already exists.
                $studyLoad = Study_Load::firstOrCreate(
                    [
                        'sl_fac_id'  => $data['fac_id'],
                        'sl_course_id' => $data['subj_id'],
                        'sl_sec_id'  => $data['sec_id'],
                        'sl_sem_id'  => $data['sem_id'],
                    ],
                    [
                        'sl_assigned_by' => Auth::id(),
                        'sl_status'      => 'approved',
                    ]
                );

                if (Schedule::where('sch_load_id', $studyLoad->sl_id)->exists()) {
                    throw new RuntimeException('DUPLICATE_LOAD');
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

                return ['success' => true, 'schedule' => $schedule->load(['subject', 'faculty', 'section', 'room'])];
            });
        } catch (RuntimeException $e) {
            if ($e->getMessage() === 'DUPLICATE_LOAD') {
                return [
                    'success'  => false,
                    'conflict' => true,
                    'message'  => 'Conflict: this teacher is already scheduled for this exact subject and section. Edit the existing schedule instead of adding a new one.',
                ];
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
        $schedule = Schedule::findOrFail($scheduleId);

        if ($conflict = $this->findConflict($data, excludeScheduleId: $scheduleId)) {
            return ['success' => false, 'conflict' => true, 'message' => $conflict];
        }

        $schedule->update([
            'sch_fac_id'     => $data['fac_id'],
            'sch_course_id'  => $data['subj_id'],
            'sch_sec_id'     => $data['sec_id'],
            'sch_room_id'    => $data['room_id'],
            'sch_sem_id'     => $data['sem_id'],
            'sch_day'        => $data['day'],
            'sch_start_time' => $data['start_time'],
            'sch_end_time'   => $data['end_time'],
        ]);

        return ['success' => true, 'schedule' => $schedule->load(['subject', 'faculty', 'section', 'room'])];
    }

    public function delete(string $scheduleId): array
    {
        $schedule = Schedule::findOrFail($scheduleId);
        $loadId = $schedule->sch_load_id;
        $schedule->delete();

        // Free up the study load so the same faculty/subject/section/semester
        // combo can be rescheduled later without hitting the unique constraint.
        Study_Load::where('sl_id', $loadId)->delete();

        return ['success' => true, 'message' => 'Schedule deleted.'];
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
