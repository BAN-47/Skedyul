<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Schedule_Submission;

class FacultyPbtApproval
{
    public function isApproved(string $facultyId, ?string $semesterId): bool
    {
        if (!$semesterId) {
            return false;
        }

        $submission = Schedule_Submission::query()
            ->where('schsub_type', 'pbt')
            ->where('schsub_fac_id', $facultyId)
            ->where('schsub_sem_id', $semesterId)
            ->orderByDesc('schsub_submitted_at')
            ->first();

        if (
            !$submission
            || $submission->schsub_status !== 'approved'
            || !is_array($submission->schsub_schedule_snapshot)
        ) {
            return false;
        }

        $currentSnapshot = Schedule::with(['faculty.user', 'subject', 'section', 'room'])
            ->where('sch_fac_id', $facultyId)
            ->where('sch_sem_id', $semesterId)
            ->where('sch_is_active', true)
            ->orderBy('sch_day')
            ->orderBy('sch_start_time')
            ->get()
            ->map(fn(Schedule $schedule) => [
                'schedule_id' => $schedule->sch_id,
                'faculty' => $schedule->faculty?->user?->usr_name
                    ?? trim(implode(' ', array_filter([
                        $schedule->faculty?->fac_first_name,
                        $schedule->faculty?->fac_middle_name,
                        $schedule->faculty?->fac_last_name,
                        $schedule->faculty?->fac_suffix,
                    ]))),
                'subject_code' => $schedule->subject?->course_code,
                'subject_name' => $schedule->subject?->course_name,
                'section' => $schedule->section?->sec_name,
                'room' => $schedule->room?->room_name,
                'day' => $schedule->sch_day,
                'start_time' => $schedule->sch_start_time,
                'end_time' => $schedule->sch_end_time,
            ])
            ->all();

        return $this->normalize($submission->schsub_schedule_snapshot)
            === $this->normalize($currentSnapshot);
    }

    private function normalize(array $rows): array
    {
        return collect($rows)
            ->map(static function ($row): array {
                $row = (array) $row;

                return [
                    'schedule_id' => trim((string) ($row['schedule_id'] ?? '')),
                    'faculty' => trim((string) ($row['faculty'] ?? '')),
                    'subject_code' => trim((string) ($row['subject_code'] ?? '')),
                    'subject_name' => trim((string) ($row['subject_name'] ?? '')),
                    'section' => trim((string) ($row['section'] ?? '')),
                    'room' => trim((string) ($row['room'] ?? '')),
                    'day' => trim((string) ($row['day'] ?? '')),
                    'start_time' => substr(trim((string) ($row['start_time'] ?? '')), 0, 5),
                    'end_time' => substr(trim((string) ($row['end_time'] ?? '')), 0, 5),
                ];
            })
            ->sortBy(static fn(array $row): string => json_encode(
                $row,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ) ?: '')
            ->values()
            ->all();
    }
}
