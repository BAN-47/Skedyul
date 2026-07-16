<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Program;
use App\Models\Workload;
use App\Models\Schedule_Submission;
use App\Models\Subjects;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;

class DeanDashboardController extends Controller
{
    // Hardcoded assumption: max weekly teaching load per faculty.
    // No config value exists for this yet — adjust here if your team defines one.
    const MAX_LOAD_HOURS = 30;

    public function index()
    {
        // ---------- ACADEMIC CONTEXT ----------
        $academicYear = AcademicYear::where('ay_is_active', true)->first();
        $semester     = Semester::where('sem_is_active', true)->first();

        // ---------- FACULTY + WORKLOAD ----------
        $faculty = Faculty::with('user', 'department')->get();
        $totalFaculty = $faculty->count();

        // Latest workload per faculty for the active semester
        $workloads = Workload::when($semester, fn($q) => $q->where('wl_sem_id', $semester->sem_id))
            ->when($academicYear, fn($q) => $q->where('wl_ay_id', $academicYear->ay_id))
            ->get()
            ->keyBy('wl_fac_id');

        $facultyLoads = $faculty->map(function ($f) use ($workloads) {
            $hours = optional($workloads->get($f->fac_id))->wl_total_hours ?? 0;
            return [
                'faculty'  => $f,
                'hours'    => $hours,
                'status'   => match (true) {
                    $hours > self::MAX_LOAD_HOURS      => 'Overload',
                    $hours >= self::MAX_LOAD_HOURS - 3 => 'Near Max',
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
        // Note: grouped by actual Department (dept_name), not by Program (BSIS/BSIT/BIT-CT).
        // Faculty links to fac_dept_id directly — there's no faculty-to-program link in the schema yet.
        $departments = Department::all();

        $deptSummary = $departments->map(function ($dept) use ($facultyLoads) {
            $deptFacultyLoads = $facultyLoads->filter(
                fn($fl) => $fl['faculty']->fac_dept_id === $dept->dept_id
            );

            $count = $deptFacultyLoads->count();
            $avgPercent = $count > 0
                ? round($deptFacultyLoads->avg('hours') / self::MAX_LOAD_HOURS * 100)
                : 0;

            $color = match (true) {
                $avgPercent >= 80 => 'green',
                $avgPercent >= 50 => 'amber',
                default           => 'red',
            };

            return [
                'name'    => $dept->dept_name,
                'count'   => $count,
                'percent' => min(100, $avgPercent),
                'color'   => $color,
            ];
        })->values();

        // ---------- SUBJECTS PLOTTED ----------
        $subjectsTotal   = Subjects::count();
        $subjectsPlotted = Subjects::whereHas('studyLoads')->count(); // subjects with at least one assigned study load

        // ---------- SCHEDULE SUBMISSIONS / APPROVALS ----------
        $pendingApprovals = Schedule_Submission::with('department')
            ->when($semester, fn($q) => $q->where('schsub_sem_id', $semester->sem_id))
            ->where('schsub_status', 'Pending')
            ->get()
            ->map(function ($sub) {
                $submitter = User::find($sub->schsub_submitted_by);
                return [
                    'submission'  => $sub,
                    'dept_name'   => $sub->department->dept_name ?? 'Unknown Dept',
                    'submitted_by'=> $submitter->usr_name ?? 'Unknown',
                    'submitted_at'=> $sub->schsub_submitted_at,
                ];
            });

        $scheduledApprovedCount = Schedule_Submission::when($semester, fn($q) => $q->where('schsub_sem_id', $semester->sem_id))
            ->where('schsub_status', 'Approved')
            ->count();

        $pendingDeptCount = $pendingApprovals->count();

        return view('dean.dean_dashboard', compact(
            'academicYear', 'semester',
            'totalFaculty', 'avgFacultyLoad', 'overloadCount',
            'facultyAlerts', 'deptSummary',
            'subjectsTotal', 'subjectsPlotted',
            'pendingApprovals', 'scheduledApprovedCount', 'pendingDeptCount'
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