<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Section;
use App\Models\Course;
use App\Models\Room;
use App\Models\Notification;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        // ---------- USERS (paginated, 10 per page) ----------
        $users = User::orderBy('usr_name')->paginate(10)->withQueryString();

        $roleCounts = [
            'faculty'          => User::where('usr_role', 'faculty')->count(),
            'department_chair' => User::where('usr_role', 'department_chair')->count(),
            'dean'             => User::where('usr_role', 'dean')->count(),
            'system_admin'     => User::where('usr_role', 'system_admin')->count(),
        ];
        $totalUsers   = array_sum($roleCounts);
        $totalFaculty = $roleCounts['faculty'];

        // ---------- ACADEMIC CONTEXT ----------
        $academicYear = AcademicYear::where('ay_is_active', true)->first();
        $semester     = Semester::where('sem_is_active', true)->first();

        // ---------- SECTIONS ----------
        $sectionAll = Section::with([
            'program.department',
            'studyLoads' => fn ($query) => $query
                ->when($semester, fn ($loads) => $loads->where('sl_sem_id', $semester->sem_id))
                ->with(['schedules' => fn ($schedules) => $schedules
                    ->where('sch_is_active', true)
                    ->when($semester, fn ($items) => $items->where('sch_sem_id', $semester->sem_id))]),
        ])
            ->when($academicYear, fn($q) => $q->where('sec_ay_id', $academicYear->ay_id))
            ->when($semester, fn($q) => $q->where('sec_sem_id', $semester->sem_id))
            ->orderBy('sec_name')
            ->get();

        // The dashboard metric is a system-wide count across all academic years
        // and semesters. Keep the active-term collection count for the table below.
        $currentTermSectionCount = $sectionAll->count();
        $totalSections = Section::count();
        $sectionAll->each(function ($section) {
            $loads = $section->studyLoads;
            $scheduledLoads = $loads->filter(fn ($load) => $load->schedules->isNotEmpty())->count();
            $totalLoads = $loads->count();

            $section->schedule_load_total = $totalLoads;
            $section->schedule_load_plotted = $scheduledLoads;
            $section->schedule_progress_percent = $totalLoads > 0
                ? (int) round(($scheduledLoads / $totalLoads) * 100)
                : 0;
            $section->schedule_progress_status = match (true) {
                $totalLoads > 0 && $scheduledLoads === $totalLoads => 'Fully Scheduled',
                $scheduledLoads > 0 => 'In Progress',
                default => 'Unscheduled',
            };
        });

        $scheduledCount   = $sectionAll->where('schedule_progress_status', 'Fully Scheduled')->count();
        $inProgressCount  = $sectionAll->where('schedule_progress_status', 'In Progress')->count();
        $unscheduledCount = $sectionAll->where('schedule_progress_status', 'Unscheduled')->count();

        // Full list for client-side JS pagination (8 per page)
        $section = $sectionAll;

        $program = $sectionAll
            ->groupBy(fn($s) => $s->program->dept_name ?? $s->program->prog_name ?? 'Unknown Department')
            ->map(function ($group, $programName) {
                $total     = $group->sum('schedule_load_total');
                $scheduled = $group->sum('schedule_load_plotted');
                $percent   = $total > 0 ? round(($scheduled / $total) * 100) : 0;

                $color = match (true) {
                    $percent >= 90 => 'green',
                    $percent >= 50 => 'amber',
                    default        => 'red',
                };

                return ['name' => $programName, 'percent' => $percent, 'color' => $color];
            })
            ->values();

        // ---------- SUBJECTS ----------
        $subjectAll = Course::with(['department', 'program'])
            ->orderBy('course_code')
            ->get();
        $subjectsOffered   = $subjectAll->count();
        // Full list for client-side JS pagination (8 per page)
        $subject = $subjectAll;

        // ---------- ROOMS ----------
        $allRooms = Room::all();

        $totalRooms     = $allRooms->count();
        $roomsAvailable = $allRooms->where('room_is_available', true)->count();
        $roomsOccupied  = $totalRooms - $roomsAvailable;
        $roomsInUse     = $roomsOccupied;

        $roomBookings = Schedule::query()
            ->select('sch_room_id', DB::raw('COUNT(*) as booking_count'))
            ->where('sch_is_active', true)
            ->groupBy('sch_room_id')
            ->pluck('booking_count', 'sch_room_id');

        $room = $allRooms->map(function ($r) use ($roomBookings) {
            $bookings = (int) ($roomBookings[$r->room_id] ?? 0);
            $percent  = min(100, round(($bookings / 40) * 100));
            $color = match (true) {
                $percent >= 80 => 'red',
                $percent >= 40 => 'amber',
                default        => 'green',
            };
            return ['name' => $r->room_name, 'count' => $bookings, 'percent' => $percent, 'color' => $color];
        });
        // ---------- NOTIFICATIONS ----------
        $notifCount = Notification::where('notif_usr_id', Auth::id())
            ->where('notif_is_read', false)
            ->count();

        // ---------- SYSTEM STATUS ----------
        $dbStatus = 'Online';
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbStatus = 'Offline';
        }

        $appHost = (string) (parse_url((string) config('app.url'), PHP_URL_HOST) ?? '');
        $platformName = str_contains(strtolower($appHost), 'laravel.cloud') ? 'Laravel Cloud' : 'Application Host';
        $databaseDriver = strtoupper((string) config('database.default'));

        $dbRecords = $totalUsers + $totalSections + $subjectsOffered + $totalRooms;

        // ---------- ALL-COLLEGE SCHEDULE ANALYTICS ----------
        // Read actual active timetable entries for the current semester. Faculty
        // may be stored directly on a schedule or inherited from its study load.
        $activeSchedules = Schedule::with([
            'faculty.user',
            'faculty.program.department',
            'studyLoad.faculty.user',
            'studyLoad.faculty.program.department',
            'studyLoad.subject',
            'studyLoad.section',
            'course',
            'section',
            'room',
        ])
            ->where('sch_is_active', true)
            ->when($semester, function ($query) use ($semester) {
                $query->where(function ($termQuery) use ($semester) {
                    $termQuery->where('sch_sem_id', $semester->sem_id)
                        ->orWhereHas('studyLoad', fn ($loadQuery) => $loadQuery->where('sl_sem_id', $semester->sem_id));
                });
            })
            ->get();

        $scheduleAnalytics = $activeSchedules->map(function (Schedule $schedule) {
            $faculty = $schedule->faculty ?? $schedule->studyLoad?->faculty;

            $minutes = 0;
            if ($schedule->sch_start_time && $schedule->sch_end_time) {
                $start = Carbon::parse($schedule->sch_start_time);
                $end = Carbon::parse($schedule->sch_end_time);
                $minutes = max(0, $start->diffInMinutes($end));
            }

            $shift = strtolower(trim((string) $schedule->sch_shift));
            if (!in_array($shift, ['day', 'evening'], true)) {
                $shift = Carbon::parse($schedule->sch_start_time ?: '07:00')->hour >= 17 ? 'evening' : 'day';
            }

            $course = $schedule->course ?? $schedule->studyLoad?->subject;
            $section = $schedule->section ?? $schedule->studyLoad?->section;
            $room = $schedule->room;
            return [
                'faculty_id' => (string) ($faculty?->fac_id ?? ''),
                'faculty' => $faculty,
                'minutes' => $minutes,
                'shift' => $shift,
                'day' => strtolower(substr(trim((string) $schedule->sch_day), 0, 3)),
                'room_id' => (string) ($schedule->sch_room_id ?? ''),
                'room_name' => (string) ($room?->room_name ?? 'Room not assigned'),
                'section_id' => (string) ($schedule->sch_sec_id ?? $schedule->studyLoad?->sl_sec_id ?? $section?->sec_id ?? ''),
                'section_name' => (string) ($section?->sec_name ?? ''),
                'class_key' => (string) ($course?->course_id ?? $schedule->sch_course_id ?? $schedule->sch_load_id ?? $schedule->sch_id)
                    . '|' . (string) ($schedule->sch_sec_id ?? $schedule->studyLoad?->sl_sec_id ?? ''),
                'course_code' => (string) ($course?->course_code ?? ''),
            ];
        })->filter();

        $topFaculty = $scheduleAnalytics->filter(fn ($entry) => $entry['faculty_id'] !== '')
            ->groupBy('faculty_id')
            ->map(function ($entries) {
                $faculty = $entries->first()['faculty'];
                $department = $faculty->program;
                $college = $department?->department;
                return [
                    'name' => $faculty->full_name ?: ($faculty->user?->usr_name ?? 'Faculty'),
                    'department' => $department?->dept_code ?? $department?->dept_name ?? 'Department not set',
                    'college' => $college?->college_code ?? $college?->college_name ?? 'College not set',
                    'day_hours' => round($entries->where('shift', 'day')->sum('minutes') / 60, 1),
                    'evening_hours' => round($entries->where('shift', 'evening')->sum('minutes') / 60, 1),
                    'total_hours' => round($entries->sum('minutes') / 60, 1),
                    'classes' => $entries->pluck('class_key')->unique()->count(),
                    'courses' => $entries->pluck('course_code')->filter()->unique()->values(),
                ];
            })
            ->sortByDesc('total_hours')
            ->take(3)
            ->values();

        $weekdayOrder = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        $weekdayLabels = ['mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday'];
        $weeklyPattern = collect($weekdayOrder)->map(function ($day) use ($scheduleAnalytics, $weekdayLabels) {
            $entries = $scheduleAnalytics->where('day', $day);
            $roomUsage = $entries->filter(fn ($entry) => $entry['room_id'] !== '')
                ->groupBy('room_id')
                ->map(function ($roomEntries) {
                    return [
                        'name' => $roomEntries->first()['room_name'],
                        'sections_count' => $roomEntries->pluck('section_id')->filter()->unique()->count(),
                        'class_count' => $roomEntries->count(),
                        'sections' => $roomEntries->pluck('section_name')->filter()->unique()->values(),
                    ];
                })
                ->sort(fn ($a, $b) => ($b['sections_count'] <=> $a['sections_count']) ?: ($b['class_count'] <=> $a['class_count']));
            $mostUsedRoom = $roomUsage->first();

            return [
                'label' => $weekdayLabels[$day],
                'short' => strtoupper($day),
                'hours' => round($entries->sum('minutes') / 60, 1),
                'classes' => $entries->count(),
                'sections' => $entries->pluck('section_id')->filter()->unique()->count(),
                'section_names' => $entries->pluck('section_name')->filter()->unique()->values(),
                'occupied_rooms' => $entries->pluck('room_id')->filter()->unique()->count(),
                'most_used_room' => $mostUsedRoom,
            ];
        });
        $totalScheduledHours = round($scheduleAnalytics->sum('minutes') / 60, 1);
        $scheduleRowsCount = $scheduleAnalytics->count();

        // ---------- DEPT FACULTY STATS ----------
        $deptFacultyStats = \App\Models\Departments::with('department')
            ->withCount(['faculty' => function ($query) {
                $query->whereHas('user', fn ($userQuery) => $userQuery->where('usr_is_active', true));
            }])
            ->orderBy('dept_name')
            ->get();

        $facultyDistributionGroups = $deptFacultyStats
            ->groupBy('dept_college_id')
            ->map(function ($departments) {
                $college = $departments->first()?->department;
                return [
                    'id' => (string) ($college?->college_id ?? 'unassigned'),
                    'code' => $college?->college_code ?: 'Unassigned',
                    'name' => $college?->college_name ?: 'College not assigned',
                    'faculty_count' => (int) $departments->sum('faculty_count'),
                    'departments' => $departments,
                ];
            })
            ->sortBy('name')
            ->values();
        $totalActiveFacultyInColleges = (int) $facultyDistributionGroups->sum('faculty_count');

        return view('admin.admin_dashboard', compact(
            'users',
            'roleCounts',
            'totalUsers',
            'totalFaculty',
            'academicYear',
            'semester',
            'section',
            'totalSections',
            'currentTermSectionCount',
            'program',
            'scheduledCount',
            'inProgressCount',
            'unscheduledCount',
            'subject',
            'subjectsOffered',
            'room',
            'totalRooms',
            'roomsInUse',
            'roomsAvailable',
            'roomsOccupied',
            'dbRecords',
            'dbStatus',
            'notifCount',
            'deptFacultyStats',
            'facultyDistributionGroups',
            'totalActiveFacultyInColleges',
            'topFaculty',
            'weeklyPattern',
            'totalScheduledHours',
            'scheduleRowsCount',
            'platformName',
            'databaseDriver'
        ));
    }
}
