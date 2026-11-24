<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Schedule_Submission;
use App\Models\Semester;
use App\Services\DeanApprovalNotifier;
use App\Services\ScheduleAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleSubmissionController extends Controller
{
    private function departmentIdsForChair(Dept_Chair $chair)
    {
        $collegeId = $chair->program?->dept_college_id ?? $chair->dc_college_id;

        if (!$collegeId) {
            return collect([$chair->dc_dept_id])->filter()->values();
        }

        return \App\Models\Departments::query()
            ->where('dept_college_id', $collegeId)
            ->pluck('dept_id');
    }

    public function index(Request $request)
    {
        $chair = Dept_Chair::with('program')->where('dc_usr_id', Auth::id())->first();

        abort_if(
            !$chair || !$chair->dc_dept_id,
            403,
            'Your account is not assigned to a program department.'
        );

        $departmentIds = $this->departmentIdsForChair($chair);

        $semester = Semester::with('academicYear')
            ->where('sem_is_active', true)
            ->first();

        $submissions = Schedule_Submission::with([
            'faculty.user',
            'section',
            'semester.academicYear',
            'submittedBy',
            'reviewedBy',
        ])
            ->whereIn('schsub_dept_id', $departmentIds)
            ->whereIn('schsub_type', ['pbs', 'pbt'])
            ->when(
                $semester,
                fn($query) => $query->where('schsub_sem_id', $semester->sem_id),
                fn($query) => $query->whereRaw('1 = 0')
            )
            ->orderByDesc('schsub_submitted_at')
            ->paginate(10)
            ->withQueryString();

        $submissions->getCollection()->transform(function ($submission) {
            $snapshot = $submission->schsub_schedule_snapshot;
            $submission->class_count = is_array($snapshot)
                ? count($snapshot)
                : $submission->schedules()->count();

            return $submission;
        });

        return view('chair.submit_dean', compact('semester', 'submissions'));
    }
    public function store(Request $request, DeanApprovalNotifier $approvalNotifier)
    {
        $chair = Dept_Chair::with('program')->where('dc_usr_id', Auth::id())->first();

        abort_if(
            !$chair || !$chair->dc_dept_id,
            403,
            'Your account is not assigned to a program department.'
        );

        $validated = $request->validate([
            'faculty_id' => ['required', 'uuid'],
        ]);

        $chairCollegeId = $chair->program?->dept_college_id ?? $chair->dc_college_id;
        abort_if(!$chairCollegeId, 403, 'Your account is not assigned to a college.');

        $faculty = Faculty::with('user')
            ->where('fac_id', $validated['faculty_id'])
            ->firstOrFail();

        // A Chair may submit a PBT for any department in their own college.
        // Store it under the faculty member's department so Dean review scoping
        // and the submitted schedule query use the same department.
        $facultyDepartment = \App\Models\Departments::query()
            ->where('dept_id', $faculty->fac_dept_id)
            ->where('dept_college_id', $chairCollegeId)
            ->first();
        abort_unless(
            $facultyDepartment,
            403,
            'You can only submit PBTs for faculty in your college.'
        );
        $departmentId = $facultyDepartment->dept_id;

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

        $scheduler = app(ScheduleAssignmentService::class);

        if (!$scheduler->scheduleDetailsAreComplete($facultySchedules)) {
            return back()
                ->with('error', 'Every class in this PBT must have a faculty member and course. A section is required except for REC.')
                ->withInput();
        }

        if ($scheduler->submissionConflictCount($allDepartmentSchedules, $faculty->fac_id) > 0) {
            return back()
                ->with('error', 'Resolve this faculty member’s schedule conflicts before submitting the PBT.')
                ->withInput();
        }

        $submission = Schedule_Submission::where('schsub_dept_id', $departmentId)
            ->where('schsub_type', 'pbt')
            ->where('schsub_sem_id', $semester->sem_id)
            ->where('schsub_fac_id', $faculty->fac_id)
            ->first();

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
            'section' => $schedule->section?->sec_name,
            'room' => $schedule->room?->room_name,
            'day' => $schedule->sch_day,
            'start_time' => $schedule->sch_start_time,
            'end_time' => $schedule->sch_end_time,
        ])->values()->all();

        // Only block a duplicate when the submitted timetable is unchanged.
        // A changed timetable is a revision and must return to Pending review.
        if (
            $submission
            && $submission->schsub_status !== 'returned'
            && $this->scheduleSnapshotsMatch($submission->schsub_schedule_snapshot, $snapshot)
        ) {
            $message = 'This PBT has already been submitted and is ' . $submission->schsub_status . '.';

            if ($request->boolean('return_to_pbt')) {
                $shift = $request->input('shift') === 'night' ? 'night' : 'day';

                return redirect()
                    ->route('chair.pbt', [
                        'faculty' => $faculty->fac_id,
                        'shift' => $shift,
                    ])
                    ->with('error', $message);
            }

            return redirect()
                ->route('chair.submit_dean')
                ->with('error', $message);
        }

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
                'schsub_type' => 'pbt',
                'schsub_sec_id' => null,
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

        $approvalNotifier->submitted($departmentId, 'pbt', $semester->sem_name ?? 'the selected semester');

        return redirect()
            ->route('chair.submit_dean')
            ->with('success', 'This faculty PBT was submitted to the dean for review.');
    }

    public function show(string $id)
    {
        $chair = Dept_Chair::with('program')->where('dc_usr_id', Auth::id())->first();
        abort_if(!$chair || !$chair->dc_dept_id, 403, 'Your account is not assigned to a program department.');
        $departmentIds = $this->departmentIdsForChair($chair);

        $submission = Schedule_Submission::with([
            'department',
            'faculty.user',
            'section',
            'semester.academicYear',
            'submittedBy',
            'reviewedBy',
        ])->findOrFail($id);

        abort_unless(
            $departmentIds->contains(fn($departmentId) => (string) $departmentId === (string) $submission->schsub_dept_id),
            403,
            'You cannot view submissions outside your college.'
        );

        $snapshot = collect($submission->schsub_schedule_snapshot ?? []);
        if ($snapshot->isEmpty()) {
            $query = Schedule::with(['faculty.user', 'subject', 'section', 'room'])
                ->where('sch_sem_id', $submission->schsub_sem_id)
                ->where('sch_is_active', true);

            if ($submission->schsub_type === 'pbs' && $submission->schsub_sec_id) {
                $query->where('sch_sec_id', $submission->schsub_sec_id);
            } elseif ($submission->schsub_fac_id) {
                $query->where('sch_fac_id', $submission->schsub_fac_id);
            }

            $snapshot = $query->orderBy('sch_day')->orderBy('sch_start_time')->get()
                ->map(fn($schedule) => [
                    'faculty' => \App\Services\FacultyNameFormatter::format($schedule->faculty) ?: 'Unknown Faculty',
                    'subject_code' => $schedule->subject?->course_code ?? '—',
                    'subject_name' => $schedule->subject?->course_name ?? '',
                    'section' => $schedule->section?->sec_name ?? '—',
                    'room' => $schedule->room?->room_name ?? '—',
                    'day' => $schedule->sch_day,
                    'start_time' => $schedule->sch_start_time,
                    'end_time' => $schedule->sch_end_time,
                ]);
        }

        $rows = $snapshot->values();

        return view('chair.submission_detail', compact('submission', 'rows'));
    }

    public function edit(string $id)
    {
        $chair = Dept_Chair::with('program')->where('dc_usr_id', Auth::id())->first();
        abort_if(!$chair || !$chair->dc_dept_id, 403, 'Your account is not assigned to a program department.');
        $departmentIds = $this->departmentIdsForChair($chair);

        $submission = Schedule_Submission::with('section')
            ->whereIn('schsub_dept_id', $departmentIds)
            ->findOrFail($id);

        if ($submission->schsub_status !== 'returned') {
            return redirect()
                ->route('chair.submit_dean')
                ->with('error', 'Only submissions returned by the Dean can be edited and resubmitted.');
        }

        if ($submission->schsub_type === 'pbs') {
            $shift = $submission->section?->sec_is_evening ? 'night' : 'day';

            return redirect()->route('chair.pbs', [
                'department' => $submission->schsub_dept_id,
                'section' => $submission->schsub_sec_id,
                'semester' => $submission->schsub_sem_id,
                'shift' => $shift,
            ]);
        }

        $scheduleRows = collect($submission->schsub_schedule_snapshot ?? []);
        $eveningCount = $scheduleRows->filter(function ($row) {
            $start = $row['start_time'] ?? null;
            if (!$start) {
                return false;
            }

            try {
                $time = \Illuminate\Support\Carbon::parse($start);
                return ($time->hour * 60 + $time->minute) >= (16 * 60);
            } catch (\Throwable $exception) {
                return false;
            }
        })->count();
        $shift = $eveningCount > ($scheduleRows->count() / 2) ? 'night' : 'day';

        return redirect()->route('chair.pbt', [
            'faculty' => $submission->schsub_fac_id,
            'semester' => $submission->schsub_sem_id,
            'shift' => $shift,
        ]);
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

    private function scheduleSnapshotsMatch(mixed $submittedSnapshot, array $currentSnapshot): bool
    {
        $normalize = static function ($rows): array {
            return collect(is_array($rows) ? $rows : [])
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
        };

        return $normalize($submittedSnapshot) === $normalize($currentSnapshot);
    }
}
