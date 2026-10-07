<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Faculty;
use App\Models\College;
use App\Models\Departments;
use App\Models\Study_Load;
use App\Models\Schedule_Submission;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Audit_Log;
use Illuminate\Support\Facades\Auth;

class DeanDashboardController extends Controller
{
    // Hardcoded assumption: max weekly teaching load per faculty.
    // No config value exists for this yet — adjust here if your team defines one.
    const FULL_TIME_MAX_HOURS = 30;
    const PART_TIME_MAX_HOURS = 22;

    public function index()
    {
        // ---------- ACADEMIC CONTEXT ----------
        $academicYear = AcademicYear::where('ay_is_active', true)->first();
        $semester     = Semester::where('sem_is_active', true)->first();

        // ---------- FACULTY + WORKLOAD ----------
        $faculty = Faculty::with('user', 'department')->get();
        $totalFaculty = $faculty->count();

        // Compute from assigned courses so old workload rows that used units
        // are not mistaken for teaching hours.
        $studyLoadsByFaculty = Study_Load::with('subject')
            ->when($semester, fn($q) => $q->where('sl_sem_id', $semester->sem_id))
            ->get()
            ->groupBy('sl_fac_id');

        $facultyLoads = $faculty->map(function ($f) use ($studyLoadsByFaculty) {
            $maxHours = $f->fac_employment_type === 'part_time'
                ? self::PART_TIME_MAX_HOURS
                : self::FULL_TIME_MAX_HOURS;
            $hours = (float) $studyLoadsByFaculty->get($f->fac_id, collect())
                ->sum(fn ($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject));
            return [
                'faculty'  => $f,
                'hours'    => $hours,
                'max_hours' => $maxHours,
                'status'   => match (true) {
                    $hours > $maxHours      => 'Overload',
                    $hours >= $maxHours - 3 => 'Near Max',
                    default                             => 'Available',
                },
            ];
        });

        $avgFacultyLoad = $totalFaculty > 0
            ? round($facultyLoads->avg('hours'))
            : 0;

        $overloadFaculty = $facultyLoads->where('status', 'Overload')->values();
        $overloadCount   = $overloadFaculty->count();

        // Table of faculty needing attention (overload or near max), sorted highest first
        $facultyAlerts = $facultyLoads
            ->whereIn('status', ['Overload', 'Near Max', 'Available'])
            ->sortByDesc('hours')
            ->take(10)
            ->values();

        // ---------- DEPARTMENT SUMMARY ----------
        // CCICT programs only: BSIS, BSIT, BIT-CT
        $ccict = College::where('college_code', 'CCICT')->first();

        $programOrder = ['BSIS' => 1, 'BSIT' => 2, 'BIT-CT' => 3];

        $programs = $ccict
            ? Departments::where('dept_college_id', $ccict->college_id)->get()
            ->sortBy(fn($p) => $programOrder[$p->dept_code] ?? 99)
            ->values()
            : collect();

        $deptSummary = $programs->map(function ($prog) use ($facultyLoads) {
            $progFacultyLoads = $facultyLoads->filter(
                fn($fl) => $fl['faculty']->fac_dept_id === $prog->dept_id
            );

            $count = $progFacultyLoads->count();
            $avgPercent = $count > 0
            ? round($progFacultyLoads->avg(fn ($fl) => $fl['hours'] / $fl['max_hours'] * 100))
                : 0;

            $color = match (true) {
                $avgPercent >= 80 => 'green',
                $avgPercent >= 50 => 'amber',
                default           => 'red',
            };

            $label = $prog->dept_code
                ? "{$prog->dept_code} — {$prog->dept_name}"
                : $prog->dept_name;

            return [
                'name'    => $label,
                'count'   => $count,
                'percent' => min(100, $avgPercent),
                'color'   => $color,
            ];
        })->values();

        // ---------- SUBJECTS PLOTTED ----------
        $subjectsTotal   = Course::count();
        $subjectsPlotted = Course::whereHas('studyLoads')->count(); // courses with at least one assigned study load

        // ---------- SCHEDULE SUBMISSIONS / APPROVALS ----------
        $pendingApprovals = Schedule_Submission::with('department')
            ->when($semester, fn($q) => $q->where('schsub_sem_id', $semester->sem_id))
            ->where('schsub_status', 'Pending')
            ->get();

        $submitterIds = $pendingApprovals->pluck('schsub_submitted_by');
        $submittersById = User::whereIn('usr_id', $submitterIds)->get()->keyBy('usr_id');

        $pendingApprovals = $pendingApprovals->map(function ($sub) use ($submittersById) {
            $submitter = $submittersById->get($sub->schsub_submitted_by);
            return [
                'submission'  => $sub,
                'dept_name'   => $sub->department->dept_name ?? 'Unknown Dept',
                'submitted_by' => $submitter->usr_name ?? 'Unknown',
                'submitted_at' => $sub->schsub_submitted_at,
            ];
        });

        $scheduledApprovedCount = Schedule_Submission::when($semester, fn($q) => $q->where('schsub_sem_id', $semester->sem_id))
            ->where('schsub_status', 'Approved')
            ->count();

        $pendingDeptCount = $pendingApprovals->count();
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
            'schsub_status'      => 'Approved',
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
            'schsub_status'      => 'Returned',
            'schsub_reviewed_by' => Auth::id(),
            'schsub_reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule returned to chair.',
        ]);
    }
}
