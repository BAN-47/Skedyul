<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Section;
use App\Models\Subjects;
use App\Models\Room;
use App\Models\Notification;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ---------- USERS ----------
        $users = User::orderBy('usr_name')->take(5)->get();

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
        $section = Section::with('program')
            ->when($academicYear, fn ($q) => $q->where('sec_ay_id', $academicYear->ay_id))
            ->when($semester, fn ($q) => $q->where('sec_sem_id', $semester->sem_id))
            ->get();

        $totalSections    = $section->count();
        $scheduledCount   = $section->where('sec_status', 'Scheduled')->count();
        $inProgressCount  = $section->where('sec_status', 'In Progress')->count();
        $unscheduledCount = $section->where('sec_status', 'Unscheduled')->count();

        $program = $section
            ->groupBy(fn ($s) => $s->program->prog_name ?? 'Unknown')
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
        $subject = Subjects::with(['department', 'program'])->get();
        $subjectsOffered    = $subject->count();
        $scheduleConflicts  = $subject->where('subj_is_active', false)->count();

        // ---------- ROOMS ----------
        $totalRooms     = Room::count();
        $roomsAvailable = Room::where('room_is_available', true)->count();
        $roomsOccupied  = $totalRooms - $roomsAvailable;
        $roomsInUse     = $roomsOccupied;

        $room = Room::all()->map(function ($r) {
    $bookings = Schedule::where('sch_room_id', $r->room_id)
        ->where('sch_is_active', true)
        ->count();

    // still calculate percent for the bar width visual, just not shown as the label
    $percent = min(100, round(($bookings / 40) * 100));

    $color = match (true) {
        $bookings >= 32 => 'red',
        $bookings >= 16 => 'amber',
        default         => 'green',
    };

    return [
        'name'    => $r->room_name,
        'count'   => $bookings,
        'percent' => $percent,
        'color'   => $color,
    ];
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
            'users', 'roleCounts', 'totalUsers', 'totalFaculty',
            'academicYear', 'semester',
            'section', 'totalSections', 'program',
            'scheduledCount', 'inProgressCount', 'unscheduledCount',
            'subject', 'subjectsOffered', 'scheduleConflicts',
            'room', 'totalRooms', 'roomsInUse', 'roomsAvailable', 'roomsOccupied',
            'audit_log', 'dbRecords', 'dbStatus', 'notifCount'
        ));
    }
}