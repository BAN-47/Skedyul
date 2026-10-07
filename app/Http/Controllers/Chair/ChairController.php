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
            ->where(function ($q) use ($deptId, $programId) {
                $q->where('fac_college_id', $deptId)
                    ->when($programId, fn ($query) => $query->where('fac_dept_id', $programId));
            })
            ->orWhereIn('fac_id', $assignedFacultyIds)
            ->get();

        $totalFaculty = $faculty->count();

        $studyLoadsByFaculty = Study_Load::with('subject')
            ->whereIn('sl_fac_id', $faculty->pluck('fac_id'))
            ->when($semester, fn ($q) => $q->where('sl_sem_id', $semester->sem_id))
            ->get()
            ->groupBy('sl_fac_id');

        $facultyLoad = $faculty->map(function ($f) use ($studyLoadsByFaculty) {
            $maxHours = \App\Services\ScheduleAssignmentService::facultyMaxHours($f);
            $totalHours = (float) $studyLoadsByFaculty->get($f->fac_id, collect())
                ->sum(fn ($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject));
            $remaining  = max(0, $maxHours - $totalHours);
            $percent    = min(100, (int) round(($totalHours / $maxHours) * 100));

            $status = match (true) {
                $totalHours >= $maxHours => 'Full',
                $totalHours >= $maxHours - 3 => 'Near Max',
                $f->fac_employment_type === 'part_time' => 'Part-time',
                default => 'OK',
            };

            return [
                'fac_id'     => $f->fac_id,
                'name'       => trim($f->fac_first_name . ' ' . $f->fac_last_name),
                'hours'      => $totalHours,
                'remaining'  => $remaining,
                'percent'    => $percent,
                'max_hours'  => $maxHours,
                'status'     => $status,
                'employment' => $f->fac_employment_type,
                'special_position' => $f->fac_special_position,
            ];
        });

        // ---------- SECTIONS IN THIS DEPARTMENT'S PROGRAMS ----------
        // Also narrowed to the chair's specific program when they have one,
        // same reasoning as $faculty above.
        $section = Section::with('program')
            ->whereHas('program', fn($q) => $q->where('dept_college_id', $deptId))
            ->when($deptChair->dc_prog_id, fn($q) => $q->where('sec_dept_id', $deptChair->dc_prog_id))
            ->when($academicYear, fn($q) => $q->where('sec_ay_id', $academicYear->ay_id))
            ->when($semester, fn($q) => $q->where('sec_sem_id', $semester->sem_id))
            ->get();

        $totalSections = $section->count();

        // ---------- SUBJECTS FOR THIS DEPARTMENT ----------
        // Same fix again: subj_prog_id narrows this to the chair's program.
        $subject = Course::where('course_college_id', $deptId)
            ->when($deptChair->dc_prog_id, fn($q) => $q->where('course_dept_id', $deptChair->dc_prog_id))
            ->get();
        $totalSubjects = $subject->count();

        $plottedSubjIds = $semester
            ? Study_Load::where('sl_sem_id', $semester->sem_id)
            ->whereIn('sl_course_id', $subject->pluck('course_id'))
            ->distinct()
            ->pluck('sl_course_id')
            : collect();

        $subjectsPlotted = $plottedSubjIds->count();

        // ---------- NOTIFICATIONS (drives the bell panel + Conflicts stat) ----------
        $notifications = Notification::where('notif_usr_id', $user->usr_id)
            ->orderByDesc('notif_created_at')
            ->limit(20)
            ->get();

        $unreadCount    = $notifications->where('notif_is_read', false)->count();
        $conflictsCount = $notifications
            ->where('notif_type', 'conflict')
            ->where('notif_is_read', false)
            ->count();

        $recentActivity = Audit_Log::where('al_usr_id', $user->usr_id)
            ->orderByDesc('al_created_at')
            ->limit(8)
            ->get();

        return view('chair.chair_dashboard', compact(
            'deptChair',
            'academicYear',
            'semester',
            'faculty',
            'totalFaculty',
            'facultyLoad',
            'section',
            'totalSections',
            'subject',
            'totalSubjects',
            'subjectsPlotted',
            'notifications',
            'unreadCount',
            'conflictsCount',
            'recentActivity'
        ));
    }

    /**
     * Mark a single notification as read.
     * Route suggestion: POST /chair/notifications/{notification}/read
     */
    public function markNotificationRead(Request $request, Notification $notification)
    {
        abort_unless($notification->notif_usr_id === Auth::id(), 403);

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
