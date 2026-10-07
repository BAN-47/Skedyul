<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Dean;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Schedule_Submission;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PendingApprovalsController extends Controller
{
    private function getUserScope()
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        if ($user->usr_role === 'system_admin') {
            return ['type' => 'all'];
        }

        if ($user->usr_role === 'dean') {
            $dean = Dean::where('dean_usr_id', $user->usr_id)->first();

            return $dean && $dean->dean_college_id
                ? ['type' => 'college', 'id' => $dean->dean_college_id]
                : false;
        }

        if ($user->usr_role === 'department_chair') {
            $chair = Dept_Chair::where('dc_usr_id', $user->usr_id)->first();

            return $chair && $chair->dc_dept_id
                ? ['type' => 'department', 'id' => $chair->dc_dept_id]
                : false;
        }

        return false;
    }

    private function applyUserScope($query, array $scope)
    {
        if ($scope['type'] === 'college') {
            $query->whereHas(
                'department',
                fn($departmentQuery) =>
                $departmentQuery->where('dept_college_id', $scope['id'])
            );
        } elseif ($scope['type'] === 'department') {
            $query->where('schsub_dept_id', $scope['id']);
        }

        return $query;
    }

    private function canAccessSubmission(array $scope, Schedule_Submission $submission): bool
    {
        if ($scope['type'] === 'all') {
            return true;
        }

        if ($scope['type'] === 'department') {
            return $submission->schsub_dept_id === $scope['id'];
        }

        return $scope['type'] === 'college'
            && $submission->department
            && $submission->department->dept_college_id === $scope['id'];
    }

    public function index()
    {
        $scope = $this->getUserScope();

        if ($scope === false) {
            abort(403, 'You do not have access to this page.');
        }

        $semester = Semester::where('sem_is_active', true)->first();

        $query = Schedule_Submission::with([
            'department',
            'faculty.user',
            'semester',
            'submittedBy',
            'reviewedBy',
        ]);

        $this->applyUserScope($query, $scope);

        $submissions = $query
            ->when(
                $semester,
                fn($submissionQuery) =>
                $submissionQuery->where('schsub_sem_id', $semester->sem_id)
            )
            ->whereNotNull('schsub_fac_id')
            ->orderByDesc('schsub_submitted_at')
            ->get()
            ->map(function ($submission) {
                $submission->faculty_count = Faculty::where(
                    'fac_dept_id',
                    $submission->schsub_dept_id
                )->count();

                $submission->conflict_count = $this->submissionConflictCount($submission);

                return $submission;
            });

        $pendingCount = $submissions->where('schsub_status', 'pending')->count();
        $approvedCount = $submissions->where('schsub_status', 'approved')->count();
        $returnedCount = $submissions->where('schsub_status', 'returned')->count();

        return view('dean.pending_approvals', compact(
            'submissions',
            'semester',
            'pendingCount',
            'approvedCount',
            'returnedCount'
        ));
    }

    public function review(string $id)
    {
        $scope = $this->getUserScope();

        if ($scope === false) {
            abort(403, 'Unauthorized action.');
        }

        $submission = Schedule_Submission::with([
            'department',
            'faculty.user',
            'semester',
            'submittedBy',
        ])->findOrFail($id);

        abort_unless(
            $this->canAccessSubmission($scope, $submission),
            403,
            'You can only review schedules for your authorized college or department.'
        );

        if (!empty($submission->schsub_schedule_snapshot)) {
            $schedules = collect($submission->schsub_schedule_snapshot)
                ->map(fn($schedule) => (object) [
                    'faculty_name' => $schedule['faculty'] ?? 'Unknown',
                    'subject_code' => $schedule['subject_code'] ?? '—',
                    'section_name' => $schedule['section'] ?? '—',
                    'room_name' => $schedule['room'] ?? '—',
                    'sch_day' => $schedule['day'] ?? '',
                    'sch_start_time' => $schedule['start_time'] ?? '00:00',
                    'sch_end_time' => $schedule['end_time'] ?? '00:00',
                    'has_conflict' => false,
                ]);
        } else {
            $scheduleQuery = Schedule::with([
                'faculty.user',
                'subject',
                'section',
                'room',
            ])
                ->where('sch_sem_id', $submission->schsub_sem_id)
                ->where('sch_is_active', true)
                ->whereHas('faculty', function ($facultyQuery) use ($submission) {
                    $facultyQuery->where('fac_dept_id', $submission->schsub_dept_id);

                    if ($submission->schsub_fac_id) {
                        $facultyQuery->where('fac_id', $submission->schsub_fac_id);
                    }
                });

            $schedules = $scheduleQuery
                ->orderBy('sch_day')
                ->orderBy('sch_start_time')
                ->get()
                ->map(function ($schedule) {
                    $schedule->has_conflict = Schedule::where('sch_fac_id', $schedule->sch_fac_id)
                        ->where('sch_day', $schedule->sch_day)
                        ->where('sch_start_time', $schedule->sch_start_time)
                        ->where('sch_sem_id', $schedule->sch_sem_id)
                        ->where('sch_id', '!=', $schedule->sch_id)
                        ->exists();

                    $schedule->faculty_name = optional($schedule->faculty?->user)->usr_name
                        ?? 'Unknown';
                    $schedule->subject_code = $schedule->subject?->course_code;
                    $schedule->section_name = $schedule->section?->sec_name;
                    $schedule->room_name = $schedule->room?->room_name;

                    return $schedule;
                });
        }

        $conflictCount = $schedules->where('has_conflict', true)->count();

        return view('dean.ReviewModalContent', compact(
            'submission',
            'schedules',
            'conflictCount'
        ));
    }

    public function approve(Request $request, string $id)
    {
        $scope = $this->getUserScope();

        if ($scope === false) {
            abort(403, 'Unauthorized action.');
        }

        $submission = Schedule_Submission::with('department')->findOrFail($id);

        abort_unless(
            $this->canAccessSubmission($scope, $submission),
            403,
            'You cannot approve a schedule outside your authorized college or department.'
        );

        if ($submission->schsub_status !== 'pending') {
            return back()->with('error', 'Only pending PBT submissions can be approved.');
        }

        if ($this->submissionConflictCount($submission) > 0) {
            return back()->with(
                'error',
                'Cannot approve this PBT because it has unresolved schedule conflicts.'
            );
        }

        try {
            $submission->update([
                'schsub_status' => 'approved',
                'schsub_reviewed_by' => Auth::id(),
                'schsub_reviewed_at' => now(),
                'schsub_remarks' => $request->input('remarks'),
            ]);
        } catch (\Throwable $exception) {
            return back()->with('error', 'Unable to approve this PBT due to a database error.');
        }

        return redirect()
            ->route('dean.pending_approvals')
            ->with('success', 'Faculty PBT approved successfully.');
    }

    public function returnToChair(Request $request, string $id)
    {
        $scope = $this->getUserScope();

        if ($scope === false) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'remarks' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $submission = Schedule_Submission::with('department')->findOrFail($id);

        abort_unless(
            $this->canAccessSubmission($scope, $submission),
            403,
            'You cannot return a schedule outside your authorized college or department.'
        );

        try {
            $submission->update([
                'schsub_status' => 'returned',
                'schsub_reviewed_by' => Auth::id(),
                'schsub_reviewed_at' => now(),
                'schsub_remarks' => $request->input('remarks'),
            ]);
        } catch (\Throwable $exception) {
            return back()->with('error', 'Unable to return this PBT due to a database error.');
        }

        return redirect()
            ->route('dean.pending_approvals')
            ->with('success', 'Faculty PBT returned to the chair with remarks.');
    }

    private function submissionConflictCount(Schedule_Submission $submission): int
    {
        if (!empty($submission->schsub_schedule_snapshot)) {
            return 0;
        }

        $query = Schedule::query()
            ->where('sch_sem_id', $submission->schsub_sem_id)
            ->where('sch_is_active', true)
            ->whereHas('faculty', function ($facultyQuery) use ($submission) {
                $facultyQuery->where('fac_dept_id', $submission->schsub_dept_id);

                if ($submission->schsub_fac_id) {
                    $facultyQuery->where('fac_id', $submission->schsub_fac_id);
                }
            })
            ->whereExists(function ($conflictQuery) {
                $conflictQuery->selectRaw('1')
                    ->from('schedule as conflicting_schedule')
                    ->whereColumn(
                        'conflicting_schedule.sch_sem_id',
                        'schedule.sch_sem_id'
                    )
                    ->whereColumn(
                        'conflicting_schedule.sch_day',
                        'schedule.sch_day'
                    )
                    ->whereColumn(
                        'conflicting_schedule.sch_start_time',
                        '<',
                        'schedule.sch_end_time'
                    )
                    ->whereColumn(
                        'conflicting_schedule.sch_end_time',
                        '>',
                        'schedule.sch_start_time'
                    )
                    ->whereColumn(
                        'conflicting_schedule.sch_id',
                        '!=',
                        'schedule.sch_id'
                    )
                    ->where('conflicting_schedule.sch_is_active', true)
                    ->where(function ($resourceQuery) {
                        $resourceQuery
                            ->whereColumn(
                                'conflicting_schedule.sch_fac_id',
                                'schedule.sch_fac_id'
                            )
                            ->orWhereColumn(
                                'conflicting_schedule.sch_room_id',
                                'schedule.sch_room_id'
                            )
                            ->orWhereColumn(
                                'conflicting_schedule.sch_sec_id',
                                'schedule.sch_sec_id'
                            );
                    });
            });

        return $query->count();
    }
}
