<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Audit_Log;
use App\Models\Course;
use App\Models\Departments;
use App\Models\Faculty;
use App\Models\Schedule_Submission;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Study_Load;
use App\Models\User;
use App\Services\FacultyNameFormatter;
use Illuminate\Support\Facades\Auth;

class DeanDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $dean = $user?->dean;
        abort_unless($user?->usr_role === 'dean' && $dean?->dean_college_id, 403, 'Your account is not assigned to a college.');
        $college = $dean->department;
        $collegeId = $dean->dean_college_id;
        $greetingName = collect([
            $dean?->dean_first_name,
            $dean?->dean_middle_name,
            $dean?->dean_last_name,
            $dean?->dean_suffix,
        ])->filter(fn($part) => filled($part))->implode(' ');
        $greetingName = $greetingName ?: ($user?->usr_name ?: 'Dean');

        $academicYear = AcademicYear::where('ay_is_active', true)->first();
        $semester = Semester::where('sem_is_active', true)->first();

        $faculty = Faculty::with('user', 'department')
            ->where('fac_college_id', $collegeId)
            ->get();
        $totalFaculty = $faculty->count();

        $studyLoadsByFaculty = Study_Load::with('subject')
            ->whereIn('sl_fac_id', $faculty->pluck('fac_id'))
            ->when($semester, fn($query) => $query->where('sl_sem_id', $semester->sem_id))
            ->get()
            ->groupBy('sl_fac_id');

        $facultyLoads = $faculty->map(function ($member) use ($studyLoadsByFaculty) {
            $loads = $studyLoadsByFaculty->get($member->fac_id, collect());
            $dayHours = (float) $loads->where('sl_shift', 'day')
                ->sum(fn($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject));
            $eveningHours = (float) $loads->where('sl_shift', 'evening')
                ->sum(fn($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject));
            $dayMaxHours = \App\Services\ScheduleAssignmentService::facultyMaxHours($member, 'day');
            $eveningMaxHours = \App\Services\ScheduleAssignmentService::facultyMaxHours($member, 'evening');
            $hours = $dayHours + $eveningHours;
            $isPartTime = $member->fac_employment_type === 'part_time';
            $maxHours = $isPartTime
                ? \App\Services\ScheduleAssignmentService::PART_TIME_MAX_HOURS
                : $dayMaxHours + $eveningMaxHours;
            $status = $hours > $maxHours
                ? 'Overload'
                : ($hours >= $maxHours - 3 ? 'Near Max' : 'Available');

            return [
                'faculty' => $member,
                'hours' => $hours,
                'max_hours' => $maxHours,
                'day_hours' => $dayHours,
                'day_max_hours' => $dayMaxHours,
                'evening_hours' => $eveningHours,
                'evening_max_hours' => $eveningMaxHours,
                'status' => $status,
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

        $programs = Departments::where('dept_college_id', $collegeId)
            ->orderBy('dept_code')
            ->get();

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

        $departmentIds = $programs->pluck('dept_id');
        $subjectsTotal = Course::whereIn('course_dept_id', $departmentIds)->count();
        $subjectsPlotted = Course::whereIn('course_dept_id', $departmentIds)
            ->whereHas('studyLoads')
            ->count();

        $weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $scheduleCountsByShift = Schedule::query()
            ->where('sch_is_active', true)
            ->whereHas('section.program', fn($query) => $query->where('dept_college_id', $collegeId))
            ->when($semester, fn($query) => $query->where('sch_sem_id', $semester->sem_id))
            ->select('sch_day', 'sch_shift', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
            ->groupBy('sch_day', 'sch_shift')
            ->get()
            ->groupBy('sch_day');
        $weeklyScheduleCounts = collect($weekDays)->map(fn($day) => [
            'day' => substr($day, 0, 3),
            'day_count' => (int) ($scheduleCountsByShift->get($day, collect())->firstWhere('sch_shift', 'day')->total ?? 0),
            'evening_count' => (int) ($scheduleCountsByShift->get($day, collect())->firstWhere('sch_shift', 'evening')->total ?? 0),
        ]);

        $pendingQuery = Schedule_Submission::with([
            'department',
            'faculty.user',
            'section',
        ])
            ->whereHas('department', fn($query) => $query->where('dept_college_id', $collegeId))
            ->when($semester, fn($query) => $query->where('schsub_sem_id', $semester->sem_id))
            ->where('schsub_status', 'pending')
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
            $isPbs = ($submission->schsub_type ?? 'pbt') === 'pbs';
            $recordName = $isPbs
                ? ($submission->section->sec_name ?? 'Program by Section')
                : (FacultyNameFormatter::format($submission->faculty) ?: 'Unknown Faculty');
            $departmentCode = $submission->department->dept_code ?? 'Department';

            $initials = collect(preg_split('/\s+/', trim($recordName)))
                ->filter()
                ->take(2)
                ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                ->implode('');

            $submittedAt = $submission->schsub_submitted_at;

            return [
                'id' => $submission->schsub_id,
                'title' => $recordName . ' — ' . $departmentCode . ($isPbs ? ' PBS' : ' PBT'),
                'detail' => 'Submitted by ' . $submitterName
                    . ($submittedAt ? ' · ' . $submittedAt->format('M j, Y g:i A') : ''),
                'initials' => $initials ?: ($isPbs ? 'PB' : 'FA'),
                'color' => $isPbs ? '#059669' : '#2563eb',
            ];
        });

        $scheduledApprovedCount = Schedule_Submission::query()
            ->whereHas('department', fn($query) => $query->where('dept_college_id', $collegeId))
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
            'college',
            'totalFaculty',
            'avgFacultyLoad',
            'overloadCount',
            'facultyAlerts',
            'deptSummary',
            'subjectsTotal',
            'subjectsPlotted',
            'weeklyScheduleCounts',
            'pendingApprovals',
            'scheduledApprovedCount',
            'pendingDeptCount',
            'recentActivity',
            'greetingName'
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
