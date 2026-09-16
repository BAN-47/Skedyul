<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\room as Room;
use App\Models\Schedule;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Section;
use App\Models\Subjects;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Study_Load;
use App\Models\Workload;
use App\Models\Notification;
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

        $deptId = $deptChair->dc_dept_id;

        // ---------- ACADEMIC CONTEXT ----------
        $academicYear = AcademicYear::where('ay_is_active', true)->first();
        $semester     = Semester::where('sem_is_active', true)->first();

        // ---------- FACULTY IN THIS DEPARTMENT ----------
        $faculty = Faculty::with('user')
            ->where('fac_dept_id', $deptId)
            ->get();

        $totalFaculty = $faculty->count();

        $facultyLoad = $faculty->map(function ($f) use ($semester, $academicYear) {
            $workload = Workload::where('wl_fac_id', $f->fac_id)
                ->when($semester, fn ($q) => $q->where('wl_sem_id', $semester->sem_id))
                ->when($academicYear, fn ($q) => $q->where('wl_ay_id', $academicYear->ay_id))
                ->first();

            $totalHours = (float) ($workload->wl_total_hours ?? 0);
            $remaining  = max(0, 30 - $totalHours);
            $percent    = min(100, (int) round(($totalHours / 30) * 100));

            $status = match (true) {
                $f->fac_employment_type === 'part_time' => 'Part-time',
                $totalHours >= 30 => 'Full',
                $totalHours >= 27 => 'Near Max',
                default => 'OK',
            };

            return [
                'fac_id'     => $f->fac_id,
                'name'       => trim($f->fac_first_name . ' ' . $f->fac_last_name),
                'hours'      => $totalHours,
                'remaining'  => $remaining,
                'percent'    => $percent,
                'status'     => $status,
                'employment' => $f->fac_employment_type,
            ];
        });

        // ---------- SECTIONS IN THIS DEPARTMENT'S PROGRAMS ----------
        $section = Section::with('program')
            ->whereHas('program', fn ($q) => $q->where('prog_dept_id', $deptId))
            ->when($academicYear, fn ($q) => $q->where('sec_ay_id', $academicYear->ay_id))
            ->when($semester, fn ($q) => $q->where('sec_sem_id', $semester->sem_id))
            ->get();

        $totalSections = $section->count();

        // ---------- SUBJECTS FOR THIS DEPARTMENT ----------
        $subject = Subjects::where('subj_dept_id', $deptId)->get();
        $totalSubjects = $subject->count();

        $plottedSubjIds = $semester
            ? Study_Load::where('sl_sem_id', $semester->sem_id)
                ->whereIn('sl_subj_id', $subject->pluck('subj_id'))
                ->distinct()
                ->pluck('sl_subj_id')
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

        return view('chair.chair_dashboard', compact(
            'deptChair', 'academicYear', 'semester',
            'faculty', 'totalFaculty', 'facultyLoad',
            'section', 'totalSections',
            'subject', 'totalSubjects', 'subjectsPlotted',
            'notifications', 'unreadCount', 'conflictsCount'
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