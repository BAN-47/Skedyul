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
        $sectionAll = Section::with('program')
            ->when($academicYear, fn($q) => $q->where('sec_ay_id', $academicYear->ay_id))
            ->when($semester, fn($q) => $q->where('sec_sem_id', $semester->sem_id))
            ->orderBy('sec_name')
            ->get();

        $totalSections    = $sectionAll->count();
        $scheduledCount   = $sectionAll->where('sec_status', 'Scheduled')->count();
        $inProgressCount  = $sectionAll->where('sec_status', 'In Progress')->count();
        $unscheduledCount = $sectionAll->where('sec_status', 'Unscheduled')->count();

        // Full list for client-side JS pagination (8 per page)
        $section = $sectionAll;

        $program = $sectionAll
            ->groupBy(fn($s) => $s->program->prog_name ?? 'Unknown')
            ->map(function ($group, $programName) {
                $total     = $group->count();
                $scheduled = $group->where('sec_status', 'Scheduled')->count();
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
        $scheduleConflicts = $subjectAll->filter(function ($c) {
            $active = $c->course_is_active ?? $c->subj_is_active ?? true;
            return !$active;
        })->count();
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

        // ---------- ACTIVITY LOG (no AuditLog model yet) ----------
        $audit_log = collect();

        $dbRecords = $totalUsers + $totalSections + $subjectsOffered + $totalRooms;

        return view('admin.admin_dashboard', compact(
            'users',
            'roleCounts',
            'totalUsers',
            'totalFaculty',
            'academicYear',
            'semester',
            'section',
            'totalSections',
            'program',
            'scheduledCount',
            'inProgressCount',
            'unscheduledCount',
            'subject',
            'subjectsOffered',
            'scheduleConflicts',
            'room',
            'totalRooms',
            'roomsInUse',
            'roomsAvailable',
            'roomsOccupied',
            'audit_log',
            'dbRecords',
            'dbStatus',
            'notifCount'
        ));
    }
}
