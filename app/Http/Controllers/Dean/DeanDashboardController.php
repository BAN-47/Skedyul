<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Audit_Log;
use App\Models\College;
use App\Models\Course;
use App\Models\Departments;
use App\Models\Faculty;
use App\Models\Schedule_Submission;
use App\Models\Semester;
use App\Models\Study_Load;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DeanDashboardController extends Controller
{
    const FULL_TIME_MAX_HOURS = 30;
    const PART_TIME_MAX_HOURS = 22;

    public function index()
    {
        $academicYear = AcademicYear::where('ay_is_active', true)->first();
        $semester = Semester::where('sem_is_active', true)->first();

        $faculty = Faculty::with('user', 'department')->get();
        $totalFaculty = $faculty->count();

        $studyLoadsByFaculty = Study_Load::with('subject')
            ->when($semester, fn($query) => $query->where('sl_sem_id', $semester->sem_id))
            ->get()
            ->groupBy('sl_fac_id');

        $facultyLoads = $faculty->map(function ($member) use ($studyLoadsByFaculty) {
            $maxHours = $member->fac_employment_type === 'part_time'
                ? self::PART_TIME_MAX_HOURS
                : self::FULL_TIME_MAX_HOURS;

            $hours = (float) $studyLoadsByFaculty
                ->get($member->fac_id, collect())
                ->sum(fn($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject));

            return [
                'faculty' => $member,
                'hours' => $hours,
                'max_hours' => $maxHours,
                'status' => match (true) {
                    $hours > $maxHours => 'Overload',
                    $hours >= $maxHours - 3 => 'Near Max',
                    default => 'Available',
                },
            ];
        });

        $avgFacultyLoad = $totalFaculty > 0
            ? round($facultyLoads->avg('hours'))
            : 0;

        $overloadFaculty = $facultyLoads->where('status', 'Overload')->values();
        $overloadCount = $overloadFaculty->count();

        $facultyAlerts = $facultyLoads
            ->whereIn('status', ['Overload', 'Near Max', 'Available'])
            ->sortByDesc('hours')
            ->take(10)
            ->values();

        // CCICT programs only: BSIS, BSIT, BIT-CT.
        $ccict = College::where('college_code', 'CCICT')->first();
        $programOrder = ['BSIS' => 1, 'BSIT' => 2, 'BIT-CT' => 3];

        $programs = $ccict
            ? Departments::where('dept_college_id', $ccict->college_id)
            ->get()
            ->sortBy(fn($program) => $programOrder[$program->dept_code] ?? 99)
            ->values()
            : collect();

        $deptSummary = $programs->map(function ($program) use ($facultyLoads) {
            $programFacultyLoads = $facultyLoads->filter(
                fn($load) => $load['faculty']->fac_dept_id === $program->dept_id
            );

            $count = $programFacultyLoads->count();
            $avgPercent = $count > 0
                ? round($programFacultyLoads->avg(
                    fn($load) => $load['hours'] / $load['max_hours'] * 100
                ))
                : 0;

            $color = match (true) {
                $avgPercent >= 80 => 'green',
                $avgPercent >= 50 => 'amber',
                default => 'red',
            };

            $label = $program->dept_code
                ? "{$program->dept_code} — {$program->dept_name}"
                : $program->dept_name;

            return [
                'name' => $label,
                'count' => $count,
                'percent' => min(100, $avgPercent),
                'color' => $color,
            ];
        })->values();

        $subjectsTotal = Course::count();
        $subjectsPlotted = Course::whereHas('studyLoads')->count();

        $pendingQuery = Schedule_Submission::with([
            'department',
            'faculty.user',
        ])
            ->when($semester, fn($query) => $query->where('schsub_sem_id', $semester->sem_id))
            ->where('schsub_status', 'pending')
            ->whereNotNull('schsub_fac_id')
            ->orderByDesc('schsub_submitted_at');

        $pendingDeptCount = (clone $pendingQuery)->count();
        $pendingApprovals = $pendingQuery->take(5)->get();

        $submitterIds = $pendingApprovals->pluck('schsub_submitted_by')->filter();
        $submittersById = User::whereIn('usr_id', $submitterIds)
            ->get()
            ->keyBy('usr_id');

        $pendingApprovals = $pendingApprovals->map(function ($submission) use ($submittersById) {
            $submitter = $submittersById->get($submission->schsub_submitted_by);
            $submitterName = $submitter->usr_name ?? 'Unknown Chair';
            $facultyName = $submission->faculty->user->usr_name ?? 'Unknown Faculty';
            $departmentCode = $submission->department->dept_code ?? 'Department';

            $initials = collect(preg_split('/\s+/', trim($facultyName)))
                ->filter()
                ->take(2)
                ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                ->implode('');

            $submittedAt = $submission->schsub_submitted_at;

            return [
                'id' => $submission->schsub_id,
                'title' => $facultyName . ' — ' . $departmentCode . ' PBT',
                'detail' => 'Submitted by ' . $submitterName
                    . ($submittedAt ? ' · ' . $submittedAt->format('M j, Y g:i A') : ''),
                'initials' => $initials ?: 'FA',
                'color' => '#2563eb',
            ];
        });

        $scheduledApprovedCount = Schedule_Submission::query()
            ->when($semester, fn($query) => $query->where('schsub_sem_id', $semester->sem_id))
            ->where('schsub_status', 'approved')
            ->count();

        $recentActivity = Audit_Log::where('al_usr_id', Auth::id())
            ->orderByDesc('al_created_at')
            ->limit(8)
            ->get();

        return view('dean.dean_dashboard', compact(
            'academicYear',
            'semester',
            'totalFaculty',
            'avgFacultyLoad',
            'overloadCount',
            'facultyAlerts',
            'deptSummary',
            'subjectsTotal',
            'subjectsPlotted',
            'pendingApprovals',
            'scheduledApprovedCount',
            'pendingDeptCount',
            'recentActivity'
        ));
    }

    public function approve(string $id)
    {
        $submission = Schedule_Submission::findOrFail($id);

        $submission->update([
            'schsub_status' => 'approved',
            'schsub_reviewed_by' => Auth::id(),
            'schsub_reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule approved successfully.',
        ]);
    }

    public function returnSubmission(string $id)
    {
        $submission = Schedule_Submission::findOrFail($id);

        $submission->update([
            'schsub_status' => 'returned',
            'schsub_reviewed_by' => Auth::id(),
            'schsub_reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule returned to chair.',
        ]);
    }
}
