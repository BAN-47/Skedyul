<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Schedule_Submission;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $chair = Dept_Chair::where('dc_usr_id', Auth::id())->first();

        abort_if(
            !$chair || !$chair->dc_dept_id,
            403,
            'Your account is not assigned to a program department.'
        );

        $departmentId = $chair->dc_dept_id;
        $semester = Semester::with('academicYear')
            ->where('sem_is_active', true)
            ->first();

        $facultyMembers = Faculty::with('user')
            ->where('fac_dept_id', $departmentId)
            ->orderBy('fac_last_name')
            ->orderBy('fac_first_name')
            ->get();

        $facultyOptions = collect();
        $selectedFaculty = null;
        $submission = null;
        $schedules = collect();
        $scheduleCount = 0;
        $conflicts = 0;
        $scheduleDetailsComplete = false;

        if ($semester) {
            $departmentSchedules = $this->departmentSchedules(
                $departmentId,
                $semester->sem_id
            );

            $scheduleCounts = (clone $departmentSchedules)
                ->get(['sch_fac_id'])
                ->countBy('sch_fac_id');

            $submissionsByFaculty = Schedule_Submission::where('schsub_dept_id', $departmentId)
                ->where('schsub_sem_id', $semester->sem_id)
                ->whereNotNull('schsub_fac_id')
                ->get()
                ->keyBy('schsub_fac_id');

            $facultyOptions = $facultyMembers
                ->map(function ($faculty) use ($scheduleCounts, $submissionsByFaculty) {
                    $facultySubmission = $submissionsByFaculty->get($faculty->fac_id);
                    $name = $faculty->user->usr_name
                        ?? trim(implode(' ', array_filter([
                            $faculty->fac_first_name,
                            $faculty->fac_middle_name,
                            $faculty->fac_last_name,
                            $faculty->fac_suffix,
                        ])));

                    return [
                        'id' => $faculty->fac_id,
                        'name' => $name ?: 'Unnamed Faculty',
                        'schedule_count' => $scheduleCounts->get($faculty->fac_id, 0),
                        'status' => $facultySubmission
                            ? ucfirst($facultySubmission->schsub_status)
                            : 'Not Submitted',
                        'submitted_at' => $facultySubmission?->schsub_submitted_at,
                    ];
                })
                ->filter(fn($option) => $option['schedule_count'] > 0)
                ->values();

            $selectedFacultyId = $request->query('faculty');

            if ($selectedFacultyId) {
                $selectedFaculty = $facultyMembers->firstWhere('fac_id', $selectedFacultyId);

                abort_if(
                    !$selectedFaculty,
                    404,
                    'The selected faculty member is not in your program department.'
                );

                $submission = $submissionsByFaculty->get($selectedFaculty->fac_id);
                $allDepartmentSchedules = (clone $departmentSchedules)->get();

                $selectedSchedules = $allDepartmentSchedules
                    ->where('sch_fac_id', $selectedFaculty->fac_id)
                    ->values();

                $scheduleCount = $selectedSchedules->count();
                $scheduleDetailsComplete = $selectedSchedules->isNotEmpty()
                    && $selectedSchedules->every(
                        fn($schedule) =>
                        $schedule->faculty
                            && $schedule->subject
                            && $schedule->section
                            && $schedule->room
                    );

                $conflicts = $this->conflictCount(
                    $allDepartmentSchedules,
                    $selectedFaculty->fac_id
                );

                $schedules = $this->departmentSchedules(
                    $departmentId,
                    $semester->sem_id
                )
                    ->where('sch_fac_id', $selectedFaculty->fac_id)
                    ->orderBy('sch_day')
                    ->orderBy('sch_start_time')
                    ->orderBy('sch_id')
                    ->paginate(10)
                    ->withQueryString();
            }
        }

        return view('chair.submit_dean', compact(
            'semester',
            'facultyOptions',
            'selectedFaculty',
            'schedules',
            'submission',
            'scheduleCount',
            'conflicts',
            'scheduleDetailsComplete'
        ));
    }

    public function store(Request $request)
    {
        $chair = Dept_Chair::where('dc_usr_id', Auth::id())->first();

        abort_if(
            !$chair || !$chair->dc_dept_id,
            403,
            'Your account is not assigned to a program department.'
        );

        $validated = $request->validate([
            'faculty_id' => ['required', 'uuid'],
        ]);

        $departmentId = $chair->dc_dept_id;
        $faculty = Faculty::with('user')
            ->where('fac_id', $validated['faculty_id'])
            ->where('fac_dept_id', $departmentId)
            ->firstOrFail();

        $semester = Semester::where('sem_is_active', true)->first();

        if (!$semester) {
            return back()->with('error', 'There is no active semester to submit.');
        }

        $departmentSchedules = $this->departmentSchedules(
            $departmentId,
            $semester->sem_id
        );

        $allDepartmentSchedules = (clone $departmentSchedules)->get();

        $facultySchedules = $allDepartmentSchedules
            ->where('sch_fac_id', $faculty->fac_id)
            ->values();

        if ($facultySchedules->isEmpty()) {
            return back()
                ->with('error', 'This faculty member has no scheduled classes to submit.')
                ->withInput();
        }

        if ($facultySchedules->contains(
            fn($schedule) =>
            !$schedule->faculty
                || !$schedule->subject
                || !$schedule->section
                || !$schedule->room
        )) {
            return back()
                ->with('error', 'Every class in this PBT must have a faculty member, course, section, and room.')
                ->withInput();
        }

        if ($this->conflictCount($allDepartmentSchedules, $faculty->fac_id) > 0) {
            return back()
                ->with('error', 'Resolve this faculty member’s schedule conflicts before submitting the PBT.')
                ->withInput();
        }

        $submission = Schedule_Submission::where('schsub_dept_id', $departmentId)
            ->where('schsub_sem_id', $semester->sem_id)
            ->where('schsub_fac_id', $faculty->fac_id)
            ->first();

        if ($submission && $submission->schsub_status !== 'returned') {
            return redirect()
                ->route('chair.submit_dean', ['faculty' => $faculty->fac_id])
                ->with(
                    'error',
                    'This PBT has already been submitted and is ' . $submission->schsub_status . '.'
                );
        }

        $facultyName = $faculty->user->usr_name
            ?? trim(implode(' ', array_filter([
                $faculty->fac_first_name,
                $faculty->fac_middle_name,
                $faculty->fac_last_name,
                $faculty->fac_suffix,
            ])));

        $snapshot = $facultySchedules->map(fn($schedule) => [
            'schedule_id' => $schedule->sch_id,
            'faculty' => $facultyName,
            'subject_code' => $schedule->subject->course_code,
            'subject_name' => $schedule->subject->course_name,
            'section' => $schedule->section->sec_name,
            'room' => $schedule->room->room_name,
            'day' => $schedule->sch_day,
            'start_time' => $schedule->sch_start_time,
            'end_time' => $schedule->sch_end_time,
        ])->values()->all();

        DB::transaction(function () use (
            $submission,
            $departmentId,
            $faculty,
            $semester,
            $snapshot
        ) {
            $values = [
                'schsub_dept_id' => $departmentId,
                'schsub_fac_id' => $faculty->fac_id,
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

        return redirect()
            ->route('chair.submit_dean', ['faculty' => $faculty->fac_id])
            ->with('success', 'This faculty PBT was submitted to the dean for review.');
    }

    private function departmentSchedules(string $departmentId, string $semesterId)
    {
        return Schedule::with(['faculty.user', 'subject', 'section', 'room'])
            ->where('sch_sem_id', $semesterId)
            ->where('sch_is_active', true)
            ->whereHas(
                'faculty',
                fn($query) => $query->where('fac_dept_id', $departmentId)
            );
    }

    private function conflictCount($schedules, string $facultyId): int
    {
        $count = 0;
        $items = $schedules->values();

        for ($left = 0; $left < $items->count(); $left++) {
            for ($right = $left + 1; $right < $items->count(); $right++) {
                $first = $items[$left];
                $second = $items[$right];

                if (
                    $first->sch_fac_id !== $facultyId
                    && $second->sch_fac_id !== $facultyId
                ) {
                    continue;
                }

                $overlaps = $first->sch_day === $second->sch_day
                    && $first->sch_start_time < $second->sch_end_time
                    && $second->sch_start_time < $first->sch_end_time;

                $sharesResource = $first->sch_fac_id === $second->sch_fac_id
                    || $first->sch_room_id === $second->sch_room_id
                    || $first->sch_sec_id === $second->sch_sec_id;

                if ($overlaps && $sharesResource) {
                    $count++;
                }
            }
        }

        return $count;
    }
}
