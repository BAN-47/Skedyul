<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use App\Models\Schedule;
use App\Models\Schedule_Submission;
use App\Models\Section;
use App\Models\Semester;
use App\Services\FacultyNameFormatter;
use App\Services\ScheduleAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PbsSubmissionController extends Controller
{
    public function store(Request $request, ScheduleAssignmentService $scheduler)
    {
        $chair = Dept_Chair::where('dc_usr_id', Auth::id())->first();
        abort_if(!$chair || !$chair->dc_dept_id, 403, 'Your account is not assigned to a department.');

        $data = $request->validate([
            'section' => ['required', 'uuid', 'exists:section,sec_id'],
            'semester' => ['required', 'uuid', 'exists:semester,sem_id'],
            'shift' => ['nullable', 'in:day,night'],
        ]);

        $section = Section::query()
            ->where('sec_id', $data['section'])
            ->where('sec_dept_id', $chair->dc_dept_id)
            ->firstOrFail();
        $semester = Semester::findOrFail($data['semester']);
        $shift = $data['shift'] ?? ($section->sec_is_evening ? 'night' : 'day');
        $returnToPbs = fn() => redirect()->route('chair.pbs', [
            'department' => $chair->dc_dept_id,
            'section' => $section->sec_id,
            'semester' => $semester->sem_id,
            'shift' => $shift,
        ]);

        $sectionSchedules = Schedule::with(['faculty.user', 'subject', 'section', 'room'])
            ->where('sch_sec_id', $section->sec_id)
            ->where('sch_sem_id', $semester->sem_id)
            ->where('sch_is_active', true)
            ->orderBy('sch_day')
            ->orderBy('sch_start_time')
            ->get();

        if ($sectionSchedules->isEmpty()) {
            return $returnToPbs()->with('error', 'Add at least one class to this PBS before submitting it.');
        }

        $complete = $scheduler->scheduleDetailsAreComplete($sectionSchedules)
            && $sectionSchedules->every(fn(Schedule $schedule) => $schedule->room !== null);
        if (!$complete) {
            return $returnToPbs()->with('error', 'Complete each PBS class with a faculty member, course, and room before submitting.');
        }

        $departmentSchedules = Schedule::with(['faculty', 'subject', 'section', 'room'])
            ->where('sch_sem_id', $semester->sem_id)
            ->where('sch_is_active', true)
            ->whereHas('faculty', fn($query) => $query->where('fac_dept_id', $chair->dc_dept_id))
            ->get();
        $facultyIds = $sectionSchedules->pluck('sch_fac_id')->filter()->unique();
        $conflictCount = $facultyIds->sum(
            fn($facultyId) => $scheduler->submissionConflictCount($departmentSchedules, (string) $facultyId)
        );

        if ($conflictCount > 0) {
            return $returnToPbs()->with('error', 'Resolve schedule conflicts before submitting this PBS to the dean.');
        }

        $snapshot = $sectionSchedules->map(fn(Schedule $schedule) => [
            'schedule_id' => $schedule->sch_id,
            'faculty' => FacultyNameFormatter::format($schedule->faculty) ?: 'Unknown Faculty',
            'subject_code' => $schedule->subject?->course_code ?? '—',
            'subject_name' => $schedule->subject?->course_name ?? '',
            'section' => $section->sec_name,
            'room' => $schedule->room?->room_name ?? '—',
            'day' => $schedule->sch_day,
            'start_time' => $schedule->sch_start_time,
            'end_time' => $schedule->sch_end_time,
        ])->values()->all();

        try {
            DB::transaction(function () use ($chair, $section, $semester, $snapshot) {
                $submission = Schedule_Submission::query()
                    ->where('schsub_type', 'pbs')
                    ->where('schsub_dept_id', $chair->dc_dept_id)
                    ->where('schsub_sem_id', $semester->sem_id)
                    ->where('schsub_sec_id', $section->sec_id)
                    ->lockForUpdate()
                    ->first();

                if ($submission && $submission->schsub_status !== 'returned') {
                    throw new \DomainException('This PBS has already been submitted and is ' . $submission->schsub_status . '.');
                }

                $values = [
                    'schsub_dept_id' => $chair->dc_dept_id,
                    'schsub_fac_id' => null,
                    'schsub_type' => 'pbs',
                    'schsub_sec_id' => $section->sec_id,
                    'schsub_sem_id' => $semester->sem_id,
                    'schsub_submitted_by' => Auth::id(),
                    'schsub_submitted_at' => now(),
                    'schsub_reviewed_by' => null,
                    'schsub_reviewed_at' => null,
                    'schsub_status' => 'pending',
                    'schsub_remarks' => null,
                    'schsub_schedule_snapshot' => $snapshot,
                ];

                if ($submission) {
                    $submission->update($values);
                } else {
                    Schedule_Submission::create($values);
                }
            });
        } catch (\DomainException $exception) {
            return $returnToPbs()->with('error', $exception->getMessage());
        }

        return $returnToPbs()->with('success', $section->sec_name . ' PBS was submitted to the dean for review.');
    }
}
