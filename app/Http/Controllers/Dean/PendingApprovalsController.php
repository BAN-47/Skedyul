<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Schedule_Submission;
use App\Models\Schedule;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PendingApprovalsController extends Controller
{
    // ── GET /dean/pending-approvals ──────────────────────────────────────
    public function index()
    {
        $semester = Semester::where('sem_is_active', true)->first();

        $submissions = Schedule_Submission::with([
                'department',
                'semester',
                'submittedBy',
                'reviewedBy',
            ])
            ->when($semester, fn($q) => $q->where('schsub_sem_id', $semester->sem_id))
            ->orderBy('schsub_submitted_at', 'desc')
            ->get()
            ->map(function ($sub) {
                $sub->faculty_count = Faculty::where('fac_dept_id', $sub->schsub_dept_id)->count();

                // Query Schedule's own direct sch_fac_id → faculty relation instead of
                // tunneling through Study_Load — this stays correct even if sch_load_id
                // linkage is inconsistent, since Schedule already carries sch_fac_id itself.
                $sub->conflict_count = Schedule::where('sch_sem_id', $sub->schsub_sem_id)
                    ->whereHas('faculty', fn($q) =>
                        $q->where('fac_dept_id', $sub->schsub_dept_id)
                    )
                    ->where('sch_is_active', true)
                    ->whereExists(function ($q) {
                        $q->selectRaw('1')
                          ->from('schedule as s2')
                          ->whereColumn('s2.sch_fac_id', 'schedule.sch_fac_id')
                          ->whereColumn('s2.sch_day', 'schedule.sch_day')
                          ->whereColumn('s2.sch_start_time', 'schedule.sch_start_time')
                          ->whereColumn('s2.sch_sem_id', 'schedule.sch_sem_id')
                          ->whereColumn('s2.sch_id', '!=', 'schedule.sch_id');
                    })
                    ->count();

                return $sub;
            });

        $pendingCount  = $submissions->where('schsub_status', 'pending')->count();
        $approvedCount = $submissions->where('schsub_status', 'approved')->count();
        $returnedCount = $submissions->where('schsub_status', 'returned')->count();

        return view('dean.pending_approvals', compact(
            'submissions',
            'semester',
            'pendingCount',
            'approvedCount',
            'returnedCount'
        ));
    }

    // ── GET /dean/pending-approvals/{id}/review (AJAX) ───────────────────
    public function review($id)
    {
        $submission = Schedule_Submission::with([
            'department',
            'semester',
            'submittedBy',
        ])->findOrFail($id);

        $schedules = Schedule::with([
                'faculty.user',
                'subject',
                'section',
                'room',
            ])
            ->where('sch_sem_id', $submission->schsub_sem_id)
            ->whereHas('faculty', fn($q) =>
                $q->where('fac_dept_id', $submission->schsub_dept_id)
            )
            ->where('sch_is_active', true)
            ->orderBy('sch_day')
            ->orderBy('sch_start_time')
            ->get()
            ->map(function ($sch) {
                $sch->has_conflict = Schedule::where('sch_fac_id', $sch->sch_fac_id)
                    ->where('sch_day', $sch->sch_day)
                    ->where('sch_start_time', $sch->sch_start_time)
                    ->where('sch_sem_id', $sch->sch_sem_id)
                    ->where('sch_id', '!=', $sch->sch_id)
                    ->exists();

                $sch->faculty_name = optional($sch->faculty->user)->usr_name ?? 'Unknown';
                $sch->subject_code = optional($sch->subject)->subj_code;
                $sch->section_name = optional($sch->section)->sec_name;
                $sch->room_name    = optional($sch->room)->room_name;

                return $sch;
            });

        $conflictCount = $schedules->where('has_conflict', true)->count();

        return view('dean.ReviewModalContent', compact(
            'submission',
            'schedules',
            'conflictCount'
        ));
    }

    // ── POST /dean/pending-approvals/{id}/approve ────────────────────────
    public function approve(Request $request, $id)
    {
        $submission = Schedule_Submission::findOrFail($id);

        $hasConflicts = Schedule::where('sch_sem_id', $submission->schsub_sem_id)
            ->whereHas('faculty', fn($q) =>
                $q->where('fac_dept_id', $submission->schsub_dept_id)
            )
            ->where('sch_is_active', true)
            ->whereExists(function ($q) {
                $q->selectRaw('1')
                  ->from('schedule as s2')
                  ->whereColumn('s2.sch_fac_id', 'schedule.sch_fac_id')
                  ->whereColumn('s2.sch_day', 'schedule.sch_day')
                  ->whereColumn('s2.sch_start_time', 'schedule.sch_start_time')
                  ->whereColumn('s2.sch_sem_id', 'schedule.sch_sem_id')
                  ->whereColumn('s2.sch_id', '!=', 'schedule.sch_id');
            })
            ->exists();

        if ($hasConflicts) {
            return back()->with('error', 'Cannot approve — schedule has unresolved conflicts. Return it to the Chair first.');
        }

        $submission->update([
            'schsub_status'      => 'approved',
            'schsub_reviewed_by' => Auth::id(),
            'schsub_reviewed_at' => now(),
            'schsub_remarks'     => $request->remarks ?? null,
        ]);

        return redirect()
            ->route('dean.pending_approvals')
            ->with('success', 'Schedule for ' . $submission->department->dept_code . ' approved successfully.');
    }

    // ── POST /dean/pending-approvals/{id}/return ─────────────────────────
    public function returnToChair(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|string|min:5|max:1000',
        ]);

        $submission = Schedule_Submission::findOrFail($id);

        $submission->update([
            'schsub_status'      => 'returned',
            'schsub_reviewed_by' => Auth::id(),
            'schsub_reviewed_at' => now(),
            'schsub_remarks'     => $request->remarks,
        ]);

        return redirect()
            ->route('dean.pending_approvals')
            ->with('success', 'Schedule returned to Chair with remarks.');
    }
}