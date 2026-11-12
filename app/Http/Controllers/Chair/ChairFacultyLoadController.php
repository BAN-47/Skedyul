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
 * Day caps follow faculty designation; evening caps are 30 hours for everyone.
 * Part-time faculty also have a 30-hour cap in each shift.
 */
class ChairFacultyLoadController extends Controller
{
    const NEAR_MAX_BUFFER     = 3;

    public const SPECIAL_POSITION_MAX_HOURS = ScheduleAssignmentService::SPECIAL_POSITION_MAX_HOURS;

    public function index(Request $request)
    {
        $deptChair = Dept_Chair::where('dc_usr_id', Auth::id())->firstOrFail();

        $department   = College::find($deptChair->dc_college_id);
        $program      = Departments::find($deptChair->dc_dept_id);
        $validated = $request->validate([
            'semester' => ['nullable', 'uuid', 'exists:semester,sem_id'],
        ]);
        $semester = !empty($validated['semester'])
            ? Semester::with('academicYear')->findOrFail($validated['semester'])
            : null;
        $semester ??= Semester::with('academicYear')->where('sem_is_active', true)->first();
        $semester ??= Semester::with('academicYear')->orderByDesc('sem_start_date')->first();
        $academicYear = $semester?->academicYear;
        $semesters = Semester::with('academicYear')
            ->orderByDesc('sem_start_date')
            ->get()
            ->map(function (Semester $option) {
                $year = $option->academicYear?->ay_academic_year
                    ?? $option->academicYear?->ay_year_label
                    ?? '';
                $yearLabel = $year !== '' ? str_replace('-', ' - ', $year) : '';
                $option->label = trim(
                    ($option->sem_name ?? '')
                    . ($yearLabel !== '' ? ', AY ' . $yearLabel : '')
                    . ($option->sem_is_active ? ' (Current)' : '')
                );

                return $option;
            });

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
            ->when(
                $semester,
                fn ($q) => $q->where('sl_sem_id', $semester->sem_id),
                fn ($q) => $q->whereRaw('1 = 0')
            )
            ->with('subject')
            ->get()
            ->groupBy('sl_fac_id');

        $facultyLoad = $faculty->map(function (Faculty $f) use ($studyLoads) {
            $allLoads = $studyLoads->get($f->fac_id, collect());

            $shifts = collect(['day', 'evening'])->mapWithKeys(function (string $shift) use ($f, $allLoads) {
                $loads = $allLoads->where('sl_shift', $shift);
                $totalUnits = $loads->sum(fn ($sl) => (float) ($sl->subject?->course_units ?? 0));
                $isPartTime = $f->fac_employment_type === 'part_time';
                $shiftHours = $loads->sum(fn ($sl) => ScheduleAssignmentService::courseHours($sl->subject));
                $combinedHours = $allLoads->sum(fn ($sl) => ScheduleAssignmentService::courseHours($sl->subject));
                $hoursAgainstLimit = $isPartTime ? $combinedHours : $shiftHours;
                $subjectCodes = $loads->map(fn ($sl) => $sl->subject?->course_code)->filter()->unique()->implode(', ');
                $maxHours = ScheduleAssignmentService::facultyMaxHours($f, $shift);
                $remaining = max(0, $maxHours - $hoursAgainstLimit);
                $nearMaxBuffer = min(self::NEAR_MAX_BUFFER, max(1, $maxHours * 0.2));

                if ($hoursAgainstLimit > $maxHours) {
                    $statusLabel = 'Over Limit'; $statusBadge = 'badge-red';
                } elseif ($hoursAgainstLimit >= $maxHours) {
                    $statusLabel = 'Full'; $statusBadge = 'badge-red';
                } elseif ($hoursAgainstLimit >= $maxHours - $nearMaxBuffer) {
                    $statusLabel = 'Near Max'; $statusBadge = 'badge-amber';
                } elseif ($hoursAgainstLimit <= 0) {
                    $statusLabel = $isPartTime ? 'Part-time' : 'Available';
                    $statusBadge = $isPartTime ? 'badge-teal' : 'badge-blue';
                } elseif ($isPartTime) {
                    $statusLabel = 'Part-time'; $statusBadge = 'badge-teal';
                } else {
                    $statusLabel = 'OK'; $statusBadge = 'badge-green';
                }

                return [$shift => [
                    'total_units' => $totalUnits,
                    'total_hours' => $shiftHours,
                    'limit_hours' => $hoursAgainstLimit,
                    'remaining' => $remaining,
                    'max_hours' => $maxHours,
                    'is_part_time' => $isPartTime,
                    'status_label' => $statusLabel,
                    'status_badge' => $statusBadge,
                    'subjects' => $subjectCodes !== '' ? $subjectCodes : '—',
                ]];
            });

            $day = $shifts['day'];
            $evening = $shifts['evening'];
            $isPartTime = $f->fac_employment_type === 'part_time';
            $totalHours = $day['total_hours'] + $evening['total_hours'];
            $totalMaxHours = $isPartTime
                ? ScheduleAssignmentService::PART_TIME_MAX_HOURS
                : $day['max_hours'] + $evening['max_hours'];
            $totalNearMaxBuffer = min(self::NEAR_MAX_BUFFER, max(1, $totalMaxHours * 0.2));
            if ($isPartTime) {
                $totalStatusLabel = $day['status_label'];
                $totalStatusBadge = $day['status_badge'];
            } elseif ($totalHours > $totalMaxHours) {
                $totalStatusLabel = 'Over Limit';
                $totalStatusBadge = 'badge-red';
            } elseif ($totalHours >= $totalMaxHours) {
                $totalStatusLabel = 'Full';
                $totalStatusBadge = 'badge-red';
            } elseif ($totalHours >= $totalMaxHours - $totalNearMaxBuffer) {
                $totalStatusLabel = 'Near Max';
                $totalStatusBadge = 'badge-amber';
            } else {
                $totalStatusLabel = 'Separate shift limits';
                $totalStatusBadge = 'badge-blue';
            }

            return [
                'id' => $f->fac_id,
                'name' => trim("{$f->fac_first_name} {$f->fac_last_name}"),
                'employment' => $f->fac_employment_type,
                'special_position' => $f->fac_special_position,
                'special_position_max_hours' => $f->fac_special_position
                    ? self::SPECIAL_POSITION_MAX_HOURS[$f->fac_special_position] ?? null : null,
                'subjects' => collect([$day['subjects'], $evening['subjects']])->reject(fn ($codes) => $codes === '—')->unique()->implode(', ') ?: '—',
                'total_units' => $day['total_units'] + $evening['total_units'],
                'total_hours' => $totalHours,
                'total_max_hours' => $totalMaxHours,
                'total_status_label' => $totalStatusLabel,
                'total_status_badge' => $totalStatusBadge,
                'day' => $day,
                'evening' => $evening,
            ];
        })->values();

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
            'semesters',
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
            'shift'   => 'nullable|string|in:day,evening',
        ]);

        $data['shift'] = $data['shift'] ?? 'day';

        $faculty = Faculty::findOrFail($data['fac_id']);
        $subject = Course::findOrFail($data['subj_id']);

        $subjectHours = \App\Services\ScheduleAssignmentService::courseHours($subject);

        $isPartTime = $faculty->fac_employment_type === 'part_time';
        $maxHours   = ScheduleAssignmentService::facultyMaxHours($faculty, $data['shift']);

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

        $scheduler = app(ScheduleAssignmentService::class);
        $currentHours = $scheduler->facultyHoursForLimit(
            $faculty,
            $data['fac_id'],
            $data['sem_id'],
            $data['shift']
        );

        if (($currentHours + $subjectHours) > $maxHours) {
            $label = $isPartTime ? 'part-time' : 'full-time';
            return response()->json([
                'success' => false,
                'message' => "Teaching-hour limit exceeded ({$label} max {$maxHours} hours"
                    . ($isPartTime ? ' shared across Day and Evening' : '') . "). "
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
                    'sl_shift'       => $data['shift'],
                    'sl_sec_id'      => $data['sec_id'],
                    'sl_sem_id'      => $data['sem_id'],
                    'sl_assigned_by' => Auth::id(),
                    'sl_assigned_at' => now(),
                ]);

                // Keep workload table in sync with weekly teaching hours.
                $newTotal = app(ScheduleAssignmentService::class)
                    ->facultyHours($data['fac_id'], $data['sem_id']);

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

            $newTotal = app(ScheduleAssignmentService::class)->facultyHours($facId, $semId);

            Workload::where('wl_fac_id', $facId)
                ->where('wl_sem_id', $semId)
                ->update(['wl_total_hours' => $newTotal]);
        });

        return response()->json(['success' => true, 'message' => 'Subject unassigned.']);
    }

}
