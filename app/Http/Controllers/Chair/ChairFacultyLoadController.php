<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use App\Models\College;
use App\Models\Departments;
use App\Models\Faculty;
use App\Models\Course;
use App\Models\Section;
use App\Models\Study_Load;
use App\Models\Semester;
use App\Models\AcademicYear;
use App\Models\Workload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\ScheduleAssignmentService;

/**
 * Faculty Load = assign subjects to teachers (study_load only).
 * Room + day/time belong to PBS / PBT schedule plotters, not here.
 *
 * Academic units are displayed separately from teaching hours.
 * Full-time max = 30 teaching hours | Part-time max = 22 teaching hours.
 */
class ChairFacultyLoadController extends Controller
{
    const FULL_TIME_MAX_HOURS = 30;
    const PART_TIME_MAX_HOURS = 22;
    const NEAR_MAX_BUFFER     = 3;

    public const SPECIAL_POSITION_MAX_HOURS = ScheduleAssignmentService::SPECIAL_POSITION_MAX_HOURS;

    public function index()
    {
        $deptChair = Dept_Chair::where('dc_usr_id', Auth::id())->firstOrFail();

        $department   = College::find($deptChair->dc_college_id);
        $program      = Departments::find($deptChair->dc_dept_id);
        $academicYear = AcademicYear::where('ay_is_active', true)->first();
        $semester     = Semester::where('sem_is_active', true)->first();

        $programSectionIds = Section::where('sec_dept_id', $deptChair->dc_dept_id)->pluck('sec_id');
        $assignedFacultyIds = $semester
            ? Study_Load::where('sl_sem_id', $semester->sem_id)
                ->whereIn('sl_sec_id', $programSectionIds)
                ->distinct()
                ->pluck('sl_fac_id')
            : collect();

        // The program assignment is authoritative. Requiring both the college
        // and program IDs excluded faculty whose college link was left stale.
        // Also retain faculty assigned to one of this program's active sections.
        $faculty = Faculty::where(function ($q) use ($deptChair, $assignedFacultyIds) {
                if ($deptChair->dc_dept_id) {
                    $q->where('fac_dept_id', $deptChair->dc_dept_id);
                } else {
                    $q->where('fac_college_id', $deptChair->dc_college_id);
                }
                if ($assignedFacultyIds->isNotEmpty()) {
                    $q->orWhereIn('fac_id', $assignedFacultyIds);
                }
            })
            ->orderBy('fac_first_name')
            ->orderBy('fac_last_name')
            ->get();

        $facultyIds = $faculty->pluck('fac_id');

        $subjects = Course::where('course_college_id', $deptChair->dc_college_id)
            ->when($deptChair->dc_dept_id, fn ($q) => $q->where('course_dept_id', $deptChair->dc_dept_id))
            ->where('course_is_active', true)
            ->orderBy('course_code')
            ->get();

        $studyLoads = Study_Load::whereIn('sl_fac_id', $facultyIds)
            ->when($semester, fn ($q) => $q->where('sl_sem_id', $semester->sem_id))
            ->with('subject')
            ->get()
            ->groupBy('sl_fac_id');

        $facultyLoad = $faculty->map(function (Faculty $f) use ($studyLoads) {
            $loads = $studyLoads->get($f->fac_id, collect());

            $totalUnits = $loads->sum(fn ($sl) => (float) ($sl->subject?->course_units ?? 0));
            $totalHours = $loads->sum(fn ($sl) => \App\Services\ScheduleAssignmentService::courseHours($sl->subject));

            $subjectCodes = $loads->map(fn ($sl) => $sl->subject?->course_code)
                ->filter()
                ->implode(', ');

            $isPartTime = $f->fac_employment_type === 'part_time';
            $maxHours   = ScheduleAssignmentService::facultyMaxHours($f);
            $remaining  = max(0, $maxHours - $totalHours);
            $nearMaxBuffer = min(self::NEAR_MAX_BUFFER, max(1, $maxHours * 0.2));

            if ($totalHours > $maxHours) {
                $statusLabel = 'Over Limit';
                $statusBadge = 'badge-red';
            } elseif ($totalHours >= $maxHours) {
                $statusLabel = 'Full';
                $statusBadge = 'badge-red';
            } elseif ($totalHours >= $maxHours - $nearMaxBuffer) {
                $statusLabel = 'Near Max';
                $statusBadge = 'badge-amber';
            } elseif ($totalHours <= 0) {
                $statusLabel = $isPartTime ? 'Part-time' : 'Available';
                $statusBadge = $isPartTime ? 'badge-teal' : 'badge-blue';
            } elseif ($isPartTime) {
                $statusLabel = 'Part-time';
                $statusBadge = 'badge-teal';
            } else {
                $statusLabel = 'OK';
                $statusBadge = 'badge-green';
            }

            if ($totalHours >= $maxHours) {
                $actionLabel = 'Full';
                $actionStyle = 'disabled';
            } elseif ($totalHours == 0) {
                $actionLabel = 'Assign';
                $actionStyle = 'primary';
            } else {
                $actionLabel = 'Assign More';
                $actionStyle = 'secondary';
            }

            return [
                'id'           => $f->fac_id,
                'name'         => trim("{$f->fac_first_name} {$f->fac_last_name}"),
                'employment'   => $f->fac_employment_type,
                'special_position' => $f->fac_special_position,
                'special_position_max_hours' => $f->fac_special_position
                    ? self::SPECIAL_POSITION_MAX_HOURS[$f->fac_special_position] ?? null
                    : null,
                'subjects'     => $subjectCodes !== '' ? $subjectCodes : '—',
                'total_units'  => $totalUnits,
                'total_hours'  => $totalHours,
                'remaining'    => $remaining,
                'max_hours'    => $maxHours,
                'is_part_time' => $isPartTime,
                'status_label' => $statusLabel,
                'status_badge' => $statusBadge,
                'action_label' => $actionLabel,
                'action_style' => $actionStyle,
            ];
        });

        // Sections for this chair's program only (no AY/sem filter — names carry year)
        $sections = Section::where('sec_dept_id', $deptChair->dc_dept_id)
            ->orderBy('sec_name')
            ->get();

        return view('chair.faculty_load', compact(
            'deptChair',
            'department',
            'program',
            'academicYear',
            'semester',
            'facultyLoad',
            'faculty',
            'subjects',
            'sections'
        ));
    }

    /**
     * Assign a subject to a faculty member for a section + semester.
     * Creates study_load only — NO schedule / room (that is PBS/PBT).
     * Updates workload total teaching hours for the semester.
     */
    public function assign(Request $request)
    {
        $data = $request->validate([
                'subj_id' => 'required|uuid|exists:course,course_id',
            'fac_id'  => 'required|uuid|exists:faculty,fac_id',
            'sec_id'  => 'required|uuid|exists:section,sec_id',
            'sem_id'  => 'required|uuid|exists:semester,sem_id',
        ]);

        $faculty = Faculty::findOrFail($data['fac_id']);
        $subject = Course::findOrFail($data['subj_id']);

        $subjectHours = \App\Services\ScheduleAssignmentService::courseHours($subject);

        $isPartTime = $faculty->fac_employment_type === 'part_time';
        $maxHours   = ScheduleAssignmentService::facultyMaxHours($faculty);

        // Avoid reporting a false overload when an assignment is duplicated.
        $exists = Study_Load::where([
            'sl_fac_id' => $data['fac_id'],
            'sl_course_id' => $data['subj_id'],
            'sl_sec_id' => $data['sec_id'],
            'sl_sem_id' => $data['sem_id'],
        ])->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This faculty is already assigned that subject for this section and semester.',
            ], 422);
        }

        // Current load from study_load (source of truth)
        $currentHours = Study_Load::where('sl_fac_id', $data['fac_id'])
            ->where('sl_sem_id', $data['sem_id'])
            ->with('subject')
            ->get()
            ->sum(fn ($sl) => \App\Services\ScheduleAssignmentService::courseHours($sl->subject));

        if (($currentHours + $subjectHours) > $maxHours) {
            $label = $isPartTime ? 'part-time' : 'full-time';
            return response()->json([
                'success' => false,
                'message' => "Teaching-hour limit exceeded ({$label} max {$maxHours} hours). "
                    . "Currently {$currentHours} hours; adding {$subjectHours} hours would total "
                    . ($currentHours + $subjectHours) . " hours.",
            ], 422);
        }

        try {
            $load = DB::transaction(function () use ($data, $subjectHours) {
                $load = Study_Load::create([
                    'sl_id'          => (string) Str::uuid(),
                    'sl_fac_id'      => $data['fac_id'],
                    'sl_course_id'   => $data['subj_id'],
                    'sl_sec_id'      => $data['sec_id'],
                    'sl_sem_id'      => $data['sem_id'],
                    'sl_assigned_by' => Auth::id(),
                    'sl_assigned_at' => now(),
                ]);

                // Keep workload table in sync with weekly teaching hours.
                $newTotal = Study_Load::where('sl_fac_id', $data['fac_id'])
                    ->where('sl_sem_id', $data['sem_id'])
                    ->with('subject')
                    ->get()
                    ->sum(fn ($sl) => \App\Services\ScheduleAssignmentService::courseHours($sl->subject));

                $semester = Semester::find($data['sem_id']);

                $wl = Workload::firstOrNew([
                    'wl_fac_id' => $data['fac_id'],
                    'wl_sem_id' => $data['sem_id'],
                ]);

                if (!$wl->exists) {
                    $wl->wl_id = (string) Str::uuid();
                }
                // wl_ay_id is NOT NULL — take it from the semester's academic year
                $wl->wl_ay_id = $semester?->sem_ay_id
                    ?? AcademicYear::where('ay_is_active', true)->value('ay_id');
                $wl->wl_total_hours = $newTotal;
                $wl->wl_type = $wl->wl_type ?? 'regular';
                $wl->save();

                return $load;
            });
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not save assignment: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Subject assigned (+{$subjectHours} teaching hours).",
            'load_id' => $load->sl_id,
        ]);
    }

    public function updateSpecialPosition(Request $request, string $facultyId)
    {
        $chair = Dept_Chair::where('dc_usr_id', Auth::id())->firstOrFail();
        $faculty = Faculty::whereKey($facultyId)
            ->where('fac_college_id', $chair->dc_college_id)
            ->when($chair->dc_dept_id, fn ($query) => $query->where('fac_dept_id', $chair->dc_dept_id))
            ->firstOrFail();

        $data = $request->validate([
            'special_position' => ['nullable', 'string', 'in:' . implode(',', array_keys(self::SPECIAL_POSITION_MAX_HOURS))],
        ]);

        $position = $data['special_position'] ?? null;
        $hours = $position ? self::SPECIAL_POSITION_MAX_HOURS[$position] : null;

        $faculty->fac_special_position = $position;
        $faculty->fac_special_position_max_hours = $hours;
        $faculty->save();

        return back()->with('success', $position
            ? "{$faculty->full_name}'s special position and {$hours}-hour limit were saved."
            : "{$faculty->full_name}'s special position was removed.");
    }

    public function unassign($id)
    {
        $load = Study_Load::findOrFail($id);

        DB::transaction(function () use ($load) {
            $facId = $load->sl_fac_id;
            $semId = $load->sl_sem_id;

            $load->delete();

            $newTotal = Study_Load::where('sl_fac_id', $facId)
                ->where('sl_sem_id', $semId)
                ->with('subject')
                ->get()
                ->sum(fn ($sl) => \App\Services\ScheduleAssignmentService::courseHours($sl->subject));

            Workload::where('wl_fac_id', $facId)
                ->where('wl_sem_id', $semId)
                ->update(['wl_total_hours' => $newTotal]);
        });

        return response()->json(['success' => true, 'message' => 'Subject unassigned.']);
    }

}
