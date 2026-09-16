<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use App\Models\Department;
use App\Models\Program;
use App\Models\Faculty;
use App\Models\Subjects;
use App\Models\Section;
use App\Models\Room;
use App\Models\Study_Load;
use App\Models\Semester;
use App\Models\AcademicYear;
use App\Services\ScheduleAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChairFacultyLoadController extends Controller
{
    // ASSUMPTION: adjust these if your team defines different caps.
    const FULL_TIME_MAX_UNITS = 30;
    const PART_TIME_MAX_UNITS = 18;
    const NEAR_MAX_BUFFER     = 3;

    public function index()
    {
        $deptChair = Dept_Chair::where('dc_usr_id', Auth::id())->firstOrFail();

        $department   = Department::find($deptChair->dc_dept_id);
        $program      = Program::find($deptChair->dc_prog_id);
        $academicYear = AcademicYear::where('ay_is_active', true)->first();
        $semester     = Semester::where('sem_is_active', true)->first();

        $faculty = Faculty::where('fac_dept_id', $deptChair->dc_dept_id)
            ->orderBy('fac_first_name')
            ->get();

        $facultyIds = $faculty->pluck('fac_id');

        $subjects = Subjects::where('subj_dept_id', $deptChair->dc_dept_id)
            ->when($deptChair->dc_prog_id, fn($q) => $q->where('subj_prog_id', $deptChair->dc_prog_id))
            ->where('subj_is_active', true)
            ->orderBy('subj_code')
            ->get();

        $subjectsById = $subjects->keyBy('subj_id');

        $studyLoads = Study_Load::whereIn('sl_fac_id', $facultyIds)
            ->when($semester, fn($q) => $q->where('sl_sem_id', $semester->sem_id))
            ->get()
            ->groupBy('sl_fac_id');

        $facultyLoad = $faculty->map(function (Faculty $f) use ($studyLoads, $subjectsById) {
            $loads = $studyLoads->get($f->fac_id, collect());

            $totalUnits = $loads->sum(function ($sl) use ($subjectsById) {
                $subj = $subjectsById->get($sl->sl_subj_id);
                return $subj ? ((float) $subj->subj_lecture_hours + (float) $subj->subj_lab_hours) : 0;
            });

            $subjectCodes = $loads->map(fn($sl) => optional($subjectsById->get($sl->sl_subj_id))->subj_code)
                ->filter()
                ->implode(', ');

            $isPartTime = $f->fac_employment_type === 'part_time';
            $maxUnits   = $isPartTime ? self::PART_TIME_MAX_UNITS : self::FULL_TIME_MAX_UNITS;
            $remaining  = max(0, $maxUnits - $totalUnits);

            if ($isPartTime) {
                $statusLabel = 'Part-time';
                $statusBadge = 'badge-teal';
            } elseif ($totalUnits >= $maxUnits) {
                $statusLabel = 'Full';
                $statusBadge = 'badge-red';
            } elseif ($totalUnits >= $maxUnits - self::NEAR_MAX_BUFFER) {
                $statusLabel = 'Near Max';
                $statusBadge = 'badge-amber';
            } elseif ($totalUnits <= $maxUnits * 0.5) {
                $statusLabel = 'Available';
                $statusBadge = 'badge-blue';
            } else {
                $statusLabel = 'OK';
                $statusBadge = 'badge-green';
            }

            if ($totalUnits >= $maxUnits) {
                $actionLabel = 'Full';
                $actionStyle = 'disabled';
            } elseif ($totalUnits == 0 || (!$isPartTime && $statusLabel === 'Available')) {
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
                'subjects'     => $subjectCodes !== '' ? $subjectCodes : '—',
                'total_units'  => $totalUnits,
                'remaining'    => $remaining,
                'max_units'    => $maxUnits,
                'is_part_time' => $isPartTime,
                'status_label' => $statusLabel,
                'status_badge' => $statusBadge,
                'action_label' => $actionLabel,
                'action_style' => $actionStyle,
            ];
        });

        $sections = Section::where('sec_prog_id', $deptChair->dc_prog_id)
            ->when($academicYear, fn($q) => $q->where('sec_ay_id', $academicYear->ay_id))
            ->when($semester, fn($q) => $q->where('sec_sem_id', $semester->sem_id))
            ->orderBy('sec_name')
            ->get();

        $rooms = Room::where('room_is_available', true)->orderBy('room_name')->get();

        return view('chair.faculty_load', compact(
            'deptChair', 'department', 'program', 'academicYear', 'semester',
            'facultyLoad', 'faculty', 'subjects', 'sections', 'rooms'
        ));
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'subj_id'        => 'required|uuid|exists:subject,subj_id',
            'fac_id'         => 'required|uuid|exists:faculty,fac_id',
            'sec_id'         => 'required|uuid|exists:section,sec_id',
            'room_id'        => 'required|uuid|exists:room,room_id',
            'sch_day'        => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'sch_start_time' => 'required',
            'sch_end_time'   => 'required',
        ]);

        $result = app(ScheduleAssignmentService::class)->assign($validated);
        $status = $result['status'];
        unset($result['status']);

        return response()->json($result, $status);
    }
}