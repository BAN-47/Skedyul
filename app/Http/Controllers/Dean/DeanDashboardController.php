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
use App\Models\Section;
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

        $programs = Departments::where('dept_college_id', $collegeId)
            ->orderBy('dept_code')
            ->get();
        $departmentIds = $programs->pluck('dept_id');

        // Count faculty whose home department belongs to this college.
        $faculty = Faculty::with(['user', 'program'])
            ->whereIn('fac_dept_id', $departmentIds)
            ->get();
        $totalFaculty = $faculty->count();

        $collegeStudyLoads = Study_Load::with(['subject', 'faculty.program'])
            ->whereHas('section', fn($query) => $query->whereIn('sec_dept_id', $departmentIds))
            ->when($semester, fn($query) => $query->where('sl_sem_id', $semester->sem_id), fn($query) => $query->whereRaw('1 = 0'))
            ->get()
            ->toBase()
            ->groupBy('sl_fac_id');
        $studyLoadsByFaculty = $collegeStudyLoads->only($faculty->pluck('fac_id')->all());

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

        $overloadFaculty = $facultyLoads->where('status', 'Overload')->values();
        $overloadCount = $overloadFaculty->count();

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

        $semesterNumber = $semester
            ? (str_contains(strtolower((string) $semester->sem_name), '2') ? 2 : (str_contains(strtolower((string) $semester->sem_name), '1') ? 1 : null))
            : null;
        $subjectsTotal = Course::whereIn('course_dept_id', $departmentIds)
            ->when($semesterNumber, fn($query) => $query->where('course_semester', $semesterNumber))
            ->count();
        $subjectsPlotted = Course::whereIn('course_dept_id', $departmentIds)
            ->when($semesterNumber, fn($query) => $query->where('course_semester', $semesterNumber))
            ->whereHas('studyLoads', function ($query) use ($semester, $departmentIds) {
                $query->when($semester, fn($loadQuery) => $loadQuery->where('sl_sem_id', $semester->sem_id), fn($loadQuery) => $loadQuery->whereRaw('1 = 0'))
                    ->whereHas('section', fn($sectionQuery) => $sectionQuery->whereIn('sec_dept_id', $departmentIds));
            })
            ->count();

        $totalSections = Section::whereIn('sec_dept_id', $departmentIds)
            ->get(['sec_dept_id', 'sec_name'])
            ->unique(fn($section) => (string) $section->sec_dept_id . '|' . strtolower(trim((string) $section->sec_name)))
            ->count();

        $weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $collegeSchedules = Schedule::query()
            ->where('sch_is_active', true)
            ->where(function ($query) use ($departmentIds) {
                $query->whereHas('section', fn($sectionQuery) => $sectionQuery->whereIn('sec_dept_id', $departmentIds))
                    ->orWhereHas('studyLoad.section', fn($sectionQuery) => $sectionQuery->whereIn('sec_dept_id', $departmentIds));
            })
            ->where(function ($query) use ($semester) {
                if ($semester) {
                    $query->where('sch_sem_id', $semester->sem_id)
                        ->orWhereHas('studyLoad', fn($loadQuery) => $loadQuery->where('sl_sem_id', $semester->sem_id));
                } else {
                    $query->whereRaw('1 = 0');
                }
            })
            ->with(['faculty.program', 'section', 'course', 'room', 'studyLoad.faculty.program', 'studyLoad.section', 'studyLoad.subject'])
            ->get()
            ->map(function (Schedule $schedule) {
                $load = $schedule->studyLoad;
                $section = $schedule->section ?? $load?->section;
                $facultyMember = $schedule->faculty ?? $load?->faculty;
                $day = strtolower(substr(trim((string) $schedule->sch_day), 0, 3));
                $shiftValue = strtolower((string) ($schedule->sch_shift ?? $load?->sl_shift ?? ''));
                $shift = str_contains($shiftValue, 'even') || $shiftValue === 'night' || ($shiftValue === '' && (bool) $section?->sec_is_evening)
                    ? 'evening'
                    : 'day';
                $hours = 0.0;
                if ($schedule->sch_start_time && $schedule->sch_end_time) {
                    $start = \Illuminate\Support\Carbon::parse($schedule->sch_start_time);
                    $end = \Illuminate\Support\Carbon::parse($schedule->sch_end_time);
                    $hours = max(0, $start->diffInMinutes($end)) / 60;
                }

                return [
                    'day_key' => $day,
                    'shift' => $shift,
                    'hours' => $hours,
                    'faculty_id' => (string) ($schedule->sch_fac_id ?? $load?->sl_fac_id ?? $facultyMember?->fac_id ?? ''),
                    'faculty' => $facultyMember,
                    'section_id' => (string) ($schedule->sch_sec_id ?? $load?->sl_sec_id ?? ''),
                    'room_id' => (string) ($schedule->sch_room_id ?? ''),
                    'room_name' => (string) ($schedule->room?->room_name ?? ''),
                ];
            });

        $weeklyCounts = $collegeSchedules->groupBy('day_key');
        $weeklyScheduleCounts = collect($weekDays)->map(function ($day) use ($weeklyCounts) {
            $items = $weeklyCounts->get(strtolower(substr($day, 0, 3)), collect());
            $roomUsage = $items->filter(fn($item) => $item['room_id'] !== '' || $item['room_name'] !== '')
                ->groupBy(fn($item) => $item['room_id'] !== '' ? $item['room_id'] : $item['room_name'])
                ->map(function ($bookings) {
                    return [
                        'name' => $bookings->first()['room_name'] ?: 'Room',
                        'bookings' => $bookings->count(),
                        'sections' => $bookings->pluck('section_id')->filter()->unique()->count(),
                    ];
                })
                ->sortByDesc('bookings');
            $peakRoom = $roomUsage->first();
            return [
                'day' => substr($day, 0, 3),
                'day_count' => $items->where('shift', 'day')->count(),
                'evening_count' => $items->where('shift', 'evening')->count(),
                'peak_room' => $peakRoom['name'] ?? null,
                'peak_room_bookings' => $peakRoom['bookings'] ?? 0,
                'peak_room_sections' => $peakRoom['sections'] ?? 0,
                'rooms_active' => $roomUsage->count(),
            ];
        });

        $topFaculty = $collegeSchedules
            ->filter(fn($item) => $item['faculty_id'] !== '' && $item['faculty'])
            ->groupBy('faculty_id')
            ->map(function ($schedules, $facultyId) {
                $facultyMember = $schedules->first()['faculty'];
                $dayHours = round($schedules->where('shift', 'day')->sum('hours'), 1);
                $eveningHours = round($schedules->where('shift', 'evening')->sum('hours'), 1);
                return [
                    'id' => $facultyId,
                    'name' => trim(implode(' ', array_filter([$facultyMember->fac_first_name, $facultyMember->fac_last_name]))) ?: 'Faculty member',
                    'department' => $facultyMember->program?->dept_code ?? 'College Faculty',
                    'rank' => $facultyMember->fac_rank ?? 'Faculty',
                    'day_hours' => $dayHours,
                    'evening_hours' => $eveningHours,
                    'total_plotted' => round($dayHours + $eveningHours, 1),
                    'class_count' => $schedules->count(),
                    'section_count' => $schedules->pluck('section_id')->filter()->unique()->count(),
                ];
            })
            ->sortByDesc('total_plotted')
            ->take(3)
            ->values();

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
        $pendingApprovals = $pendingQuery->paginate(5, ['*'], 'pending_page')->withQueryString();

        $submitterIds = $pendingApprovals->getCollection()->pluck('schsub_submitted_by')->filter();
        $submittersById = User::whereIn('usr_id', $submitterIds)
            ->get()
            ->keyBy('usr_id');

        $pendingApprovals->setCollection($pendingApprovals->getCollection()->map(function ($submission) use ($submittersById) {
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
        }));

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
            'totalSections',
            'overloadCount',
            'deptSummary',
            'subjectsTotal',
            'subjectsPlotted',
            'weeklyScheduleCounts',
            'topFaculty',
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
