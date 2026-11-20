<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\room as Room;
use App\Models\Schedule;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Section;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Study_Load;
use App\Models\Notification;
use App\Models\Audit_Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ChairController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ---------- WHICH DEPARTMENT DOES THIS CHAIR MANAGE? ----------
        $deptChair = Dept_Chair::with(['department', 'program'])
            ->where('dc_usr_id', $user->usr_id)
            ->first();

        if (!$deptChair) {
            abort(403, 'Your account is not assigned as a department chair.');
        }

        $deptId = $deptChair->dc_college_id;

        // ---------- ACADEMIC CONTEXT ----------
        $academicYear = AcademicYear::where('ay_is_active', true)->first();
        $semester     = Semester::where('sem_is_active', true)->first();
        $timetableTerm = trim(collect([
            $semester?->sem_name,
            $academicYear?->ay_academic_year ? 'AY ' . $academicYear->ay_academic_year : null,
        ])->filter()->implode(' · '));

        // ---------- FACULTY IN THIS DEPARTMENT ----------
        // Scoped down to the chair's specific program (fac_prog_id) when they
        // have one — a chair only manages faculty actually assigned to their
        // program, not the whole department (same pattern used everywhere
        // else a chair is program-specific).
        $programId = $deptChair->dc_dept_id;
        $assignedFacultyIds = $semester && $programId
            ? Study_Load::where('sl_sem_id', $semester->sem_id)
                ->whereHas('section', fn ($q) => $q->where('sec_dept_id', $programId))
                ->distinct()
                ->pluck('sl_fac_id')
            : collect();

        $faculty = Faculty::with('user')
            ->where('fac_is_placeholder', false)
            ->where(function ($q) use ($deptId, $programId, $assignedFacultyIds) {
                if ($programId) {
                    // Program assignment is authoritative; tolerate an outdated
                    // college link, and include faculty actively assigned to the program.
                    $q->where('fac_dept_id', $programId);
                } else {
                    $q->where('fac_college_id', $deptId);
                }
                if ($assignedFacultyIds->isNotEmpty()) {
                    $q->orWhereIn('fac_id', $assignedFacultyIds);
                }
            })
            ->get();

        $totalFaculty = $faculty->count();

        $studyLoadsByFaculty = Study_Load::with(['subject', 'section', 'schedules'])
            ->whereIn('sl_fac_id', $faculty->pluck('fac_id'))
            ->when($semester, fn ($q) => $q->where('sl_sem_id', $semester->sem_id))
            ->get()
            ->groupBy('sl_fac_id');

        // ---------- LEADERS & LOAD ANALYTICS ----------
        $facultyLoad = $faculty->map(function ($f) use ($studyLoadsByFaculty) {
            $allLoads = $studyLoadsByFaculty->get($f->fac_id, collect());

            $shifts = collect(['day', 'evening'])->mapWithKeys(function ($shift) use ($f, $allLoads) {
                $loads = $allLoads->filter(fn ($load) =>
                    ($load->section?->sec_is_evening ? 'evening' : ($load->sl_shift ?? 'day')) === $shift
                );
                $maxHours = \App\Services\ScheduleAssignmentService::facultyMaxHours($f, $shift);
                $shiftHours = \App\Services\ScheduleAssignmentService::scheduledHoursForLoads($loads);
                $combinedHours = $f->fac_employment_type === 'part_time'
                    ? \App\Services\ScheduleAssignmentService::scheduledHoursForLoads($allLoads)
                    : null;
                $hoursAgainstLimit = $combinedHours ?? $shiftHours;
                $remaining = max(0, $maxHours - $hoursAgainstLimit);
                $percent = min(100, (int) round(($hoursAgainstLimit / max(1, $maxHours)) * 100));

                $status = match (true) {
                    $hoursAgainstLimit > $maxHours => 'Overload',
                    $hoursAgainstLimit >= $maxHours => 'Full',
                    $hoursAgainstLimit >= $maxHours - 3 => 'Near Max',
                    $f->fac_employment_type === 'part_time' => 'Part-time',
                    default => 'OK',
                };

                return [$shift => [
                    'hours' => $shiftHours,
                    'limit_hours' => $hoursAgainstLimit,
                    'remaining' => $remaining,
                    'percent' => $percent,
                    'max_hours' => $maxHours,
                    'status' => $status,
                ]];
            });

            return [
                'fac_id' => $f->fac_id,
                'name' => trim($f->fac_first_name . ' ' . $f->fac_last_name),
                'rank' => $f->fac_rank,
                'employment' => $f->fac_employment_type,
                'special_position' => $f->fac_special_position,
                'day' => $shifts['day'],
                'evening' => $shifts['evening'],
                'classes' => $allLoads->unique(fn ($load) => (string) $load->sl_course_id . '|' . (string) $load->sl_sec_id)->count(),
                'total_plotted' => $shifts['day']['hours'] + $shifts['evening']['hours'],
            ];
        })->values();

        // Reindex after sorting: collection sorting preserves original keys,
        // but the dashboard uses these keys as the three medal ranks.
        $topFaculty = $facultyLoad->sortByDesc('total_plotted')->take(3)->values();

        $topFacultyIds = $topFaculty->pluck('fac_id');
        $topFacultySchedules = Schedule::query()
            ->where('sch_is_active', true)
            ->where(function ($query) use ($topFacultyIds) {
                $query->whereIn('sch_fac_id', $topFacultyIds)
                    ->orWhereHas('studyLoad', fn ($load) => $load->whereIn('sl_fac_id', $topFacultyIds));
            })
            ->when($semester, function ($query) use ($semester) {
                $query->where(function ($semesterQuery) use ($semester) {
                    $semesterQuery->where('sch_sem_id', $semester->sem_id)
                        ->orWhereHas('studyLoad', fn ($load) => $load->where('sl_sem_id', $semester->sem_id));
                });
            }, fn ($query) => $query->whereRaw('1 = 0'))
            ->with(['course', 'subject', 'section', 'room', 'studyLoad.subject', 'studyLoad.section'])
            ->orderBy('sch_start_time')
            ->get()
            ->map(function (Schedule $schedule) {
                $load = $schedule->studyLoad;
                $course = $schedule->course ?? $schedule->subject ?? $load?->subject;
                $section = $schedule->section ?? $load?->section;
                // Rooms belong to schedules; Study_Load has no room relation.
                $room = $schedule->room;
                $shiftValue = strtolower((string) ($schedule->sch_shift ?? ''));
                $isEvening = str_contains($shiftValue, 'even')
                    || $shiftValue === 'night'
                    || ($shiftValue === '' && (bool) $section?->sec_is_evening);

                return [
                    'faculty_id' => (string) ($schedule->sch_fac_id ?? $load?->sl_fac_id ?? ''),
                    'day' => (string) ($schedule->sch_day ?? ''),
                    'start' => $schedule->sch_start_time ? \Illuminate\Support\Carbon::parse($schedule->sch_start_time)->format('g:i A') : '',
                    'end' => $schedule->sch_end_time ? \Illuminate\Support\Carbon::parse($schedule->sch_end_time)->format('g:i A') : '',
                    'shift' => $isEvening ? 'evening' : 'day',
                    'course_code' => (string) ($course?->course_code ?? 'Course'),
                    'course_name' => (string) ($course?->course_name ?? ''),
                    'section' => (string) ($section?->sec_name ?? 'Section not set'),
                    'section_id' => (string) ($schedule->sch_sec_id ?? $load?->sl_sec_id ?? ''),
                    'department_id' => (string) ($section?->sec_dept_id ?? ''),
                    'room' => (string) ($room?->room_name ?? 'Room not set'),
                    'room_id' => (string) ($schedule->sch_room_id ?? ''),
                ];
            })
            ->groupBy('faculty_id')
            ->map(fn ($schedules) => $schedules->values());

        // ---------- SECTIONS IN THIS DEPARTMENT'S PROGRAMS ----------
        // Also narrowed to the chair's specific program when they have one,
        // same reasoning as $faculty above.
        $section = Section::with('program')
            ->when($programId, fn ($query) => $query->where('sec_dept_id', $programId), fn ($query) => $query->whereRaw('1 = 0'))
            ->get();

        $totalSections = $section->count();

        // Count department sections that have an active scheduled class this term.
        $sectionsPlotted = $semester && $programId
            ? Schedule::query()
                ->where('sch_is_active', true)
                ->where(function ($query) use ($semester) {
                    $query->where('sch_sem_id', $semester->sem_id)
                        ->orWhereHas('studyLoad', fn ($load) => $load->where('sl_sem_id', $semester->sem_id));
                })
                ->where(function ($query) use ($programId) {
                    $query->whereHas('section', fn ($section) => $section->where('sec_dept_id', $programId))
                        ->orWhereHas('studyLoad.section', fn ($section) => $section->where('sec_dept_id', $programId));
                })
                ->with(['section', 'studyLoad.section'])
                ->get()
                ->map(fn ($schedule) => $schedule->sch_sec_id ?? $schedule->studyLoad?->sl_sec_id)
                ->filter()
                ->unique()
                ->count()
            : 0;

        $weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $weeklySchedules = Schedule::query()
            ->where(function ($query) use ($faculty) {
                $query->whereIn('sch_fac_id', $faculty->pluck('fac_id'))
                    ->orWhereHas('studyLoad', fn ($load) => $load->whereIn('sl_fac_id', $faculty->pluck('fac_id')));
            })
            ->where('sch_is_active', true)
            ->when($semester, function ($query) use ($semester) {
                $query->where(function ($semesterQuery) use ($semester) {
                    $semesterQuery->where('sch_sem_id', $semester->sem_id)
                        ->orWhereHas('studyLoad', fn ($load) => $load->where('sl_sem_id', $semester->sem_id));
                });
            }, fn ($query) => $query->whereRaw('1 = 0'))
            ->with(['room', 'section', 'studyLoad.section'])
            ->get();
        $weeklyScheduleCounts = collect($weekDays)->map(function ($day) use ($weeklySchedules) {
            $daySchedules = $weeklySchedules->filter(fn ($schedule) => strtolower((string) $schedule->sch_day) === strtolower($day));
            $hours = $daySchedules->sum(function ($schedule) {
                if (!$schedule->sch_start_time || !$schedule->sch_end_time) return 0;
                $start = \Illuminate\Support\Carbon::parse($schedule->sch_start_time);
                $end = \Illuminate\Support\Carbon::parse($schedule->sch_end_time);
                if ($end->lessThanOrEqualTo($start)) $end->addDay();
                return max(0, $start->diffInMinutes($end) / 60);
            });
            $classCount = $daySchedules->count();
            $roomSchedules = $daySchedules->whereNotNull('sch_room_id')->groupBy('sch_room_id');
            $roomBreakdown = $roomSchedules->map(function ($schedules, $roomId) {
                $room = $schedules->first()?->room;
                $sectionIds = $schedules->map(fn ($schedule) => $schedule->sch_sec_id ?? $schedule->studyLoad?->sl_sec_id)->filter()->unique();
                return [
                    'room' => (string) ($room?->room_name ?? 'Room'),
                    'sections' => $sectionIds->count(),
                ];
            })->sortByDesc('sections')->values();
            $occupiedSectionCount = $roomSchedules->flatten(1)
                ->map(fn ($schedule) => $schedule->sch_sec_id ?? $schedule->studyLoad?->sl_sec_id)
                ->filter()->unique()->count();

            return [
                'day' => substr($day, 0, 3),
                'name' => $day,
                'count' => $classCount,
                'hours' => round($hours, 1),
                'rooms' => $roomSchedules->count(),
                'occupied_sections' => $occupiedSectionCount,
                'room_breakdown' => $roomBreakdown,
                'room_percent' => $classCount ? (int) round($occupiedSectionCount / $classCount * 100) : 0,
            ];
        });
        $weeklyRoomGroups = $weeklySchedules->whereNotNull('sch_room_id')->groupBy('sch_room_id');
        $topOccupiedRoom = $weeklyRoomGroups->map(function ($schedules) {
            $first = $schedules->first();
            $sectionIds = $schedules->map(fn ($schedule) => $schedule->sch_sec_id ?? $schedule->studyLoad?->sl_sec_id)->filter()->unique();
            return ['name' => $first?->room?->room_name ?? 'Room', 'sections' => $sectionIds->count()];
        })->sortByDesc('sections')->first();
        $roomUtilSummary = [
            'occupied_sections' => $weeklySchedules->filter(fn ($schedule) => $schedule->sch_room_id)
                ->map(fn ($schedule) => $schedule->sch_sec_id ?? $schedule->studyLoad?->sl_sec_id)->filter()->unique()->count(),
            'top_room_name' => $topOccupiedRoom['name'] ?? 'No room assigned',
            'top_room_sections' => $topOccupiedRoom['sections'] ?? 0,
        ];

        // ---------- NOTIFICATIONS (drives the bell panel + Conflicts stat) ----------
        $notifications = Notification::where('notif_usr_id', $user->usr_id)
            ->orderByDesc('notif_created_at')
            ->limit(20)
            ->get();

        $unreadCount    = $notifications->where('notif_is_read', false)->count();
        $facultyAtMaxLoadCount = $facultyLoad
            ->filter(fn ($load) =>
                $load['day']['limit_hours'] >= $load['day']['max_hours']
                || (
                    $load['employment'] !== 'part_time'
                    && $load['evening']['limit_hours'] >= $load['evening']['max_hours']
                )
            )
            ->count();

        $recentActivity = Audit_Log::where('al_usr_id', $user->usr_id)
            ->orderByDesc('al_created_at')
            ->limit(8)
            ->get();

        return view('chair.chair_dashboard', compact(
            'deptChair',
            'academicYear',
            'semester',
            'timetableTerm',
            'faculty',
            'totalFaculty',
            'facultyLoad',
            'weeklyScheduleCounts',
            'roomUtilSummary',
            'section',
            'totalSections',
            'sectionsPlotted',
            'topFaculty',
            'topFacultySchedules',
            'notifications',
            'unreadCount',
            'facultyAtMaxLoadCount',
            'recentActivity'
        ));
    }

    /**
     * Mark a single notification as read.
     * Route suggestion: POST /chair/notifications/{notification}/read
     */
    public function markNotificationRead(Request $request, Notification $notification)
    {
        abort_unless((string) $notification->notif_usr_id === (string) Auth::id(), 403);

        $notification->update(['notif_is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark every notification for this chair as read.
     * Route suggestion: POST /chair/notifications/read-all
     */
    public function markAllNotificationsRead(Request $request)
    {
        Notification::where('notif_usr_id', Auth::id())
            ->where('notif_is_read', false)
            ->update(['notif_is_read' => true]);

        return response()->json(['success' => true]);
    }
}
