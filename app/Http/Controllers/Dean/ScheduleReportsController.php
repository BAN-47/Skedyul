<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\Faculty;
use App\Models\Study_Load;
use App\Models\Semester;
use App\Models\College;
use App\Models\Departments;
use App\Models\Dept_Chair;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleReportsController extends Controller
{
    public function index()
    {
        $activeSemester = Semester::where('sem_is_active', true)->first();

        $dean = Auth::user()?->dean;
        $college = $dean?->department
            ?? College::where('college_code', 'CCICT')->first();
        $collegeId = $college?->college_id;

        // Keep department ordering consistent in all three report tabs.
        $departmentOrder = [
            'IT' => 1,
            'BSIT' => 1,
            'IS' => 2,
            'BSIS' => 2,
            'BIT-CT' => 3,
        ];
        $departmentSortKey = static function ($department) use ($departmentOrder): string {
            $code = strtoupper(trim((string) ($department?->dept_code ?? '')));
            $position = $departmentOrder[$code] ?? 99;

            return str_pad((string) $position, 2, '0', STR_PAD_LEFT) . '|' . $code;
        };
        $dayOrder = [
            'MONDAY' => 1,
            'TUESDAY' => 2,
            'WEDNESDAY' => 3,
            'THURSDAY' => 4,
            'FRIDAY' => 5,
            'SATURDAY' => 6,
            'SUNDAY' => 7,
        ];

        $scheduleQuery = Schedule::with([
            'section.program.department',
            'subject',
            'faculty.user',
            'room',
        ])->where('sch_is_active', true);

        if ($collegeId) {
            $scheduleQuery->whereHas('section.program', function ($query) use ($collegeId) {
                $query->where('dept_college_id', $collegeId);
            });
        }

        $scheduleRows = $scheduleQuery
            ->get()
            ->sortBy(function ($schedule) use ($departmentSortKey, $dayOrder) {
                $department = $schedule->section?->program;
                $day = strtoupper((string) ($schedule->sch_day ?? ''));

                return implode('|', [
                    $departmentSortKey($department),
                    strtolower((string) ($schedule->section?->sec_name ?? '')),
                    strtolower((string) ($schedule->subject?->course_code ?? '')),
                    str_pad((string) ($dayOrder[$day] ?? 99), 2, '0', STR_PAD_LEFT),
                    (string) ($schedule->sch_start_time ?? ''),
                    strtolower((string) ($schedule->faculty?->fac_last_name ?? '')),
                ]);
            })
            ->values();

        $departmentQuery = Departments::with('department');
        if ($collegeId) {
            $departmentQuery->where('dept_college_id', $collegeId);
        }

        $departments = $departmentQuery
            ->get()
            ->sortBy(fn($department) => $departmentSortKey($department))
            ->values();

        $byDepartment = $departments
            ->map(function ($department) use ($scheduleRows, $college) {
                $departmentSchedules = $scheduleRows
                    ->filter(fn($schedule) => $schedule->section?->program?->dept_id === $department->dept_id)
                    ->values();

                return [
                    'department' => $department,
                    'college' => $department->department ?? $college,
                    'schedules' => $departmentSchedules,
                ];
            })
            ->values();

        $facultyQuery = Faculty::with(['user', 'program.department']);
        if ($collegeId) {
            $facultyQuery->whereHas('program', function ($query) use ($collegeId) {
                $query->where('dept_college_id', $collegeId);
            });
        }

        $faculty = $facultyQuery
            ->get()
            ->sortBy(function ($member) use ($departmentSortKey) {
                return implode('|', [
                    $departmentSortKey($member->program),
                    strtolower((string) ($member->fac_last_name ?? '')),
                    strtolower((string) ($member->fac_first_name ?? '')),
                ]);
            })
            ->values();

        $loadsByFaculty = Study_Load::with('subject')
            ->whereIn('sl_fac_id', $faculty->pluck('fac_id'))
            ->when($activeSemester, fn($query) => $query->where('sl_sem_id', $activeSemester->sem_id))
            ->get()
            ->groupBy('sl_fac_id');

        $facultyByDepartment = $faculty->groupBy(fn($member) => $member->program?->dept_id);

        // Keep each department in its own report group instead of mixing faculty
        // from the college into one long table.
        $byFaculty = $departments
            ->map(function ($department) use ($facultyByDepartment, $loadsByFaculty, $college) {
                $departmentFaculty = $facultyByDepartment
                    ->get($department->dept_id, collect())
                    ->map(function ($member) use ($loadsByFaculty) {
                        $loads = $loadsByFaculty->get($member->fac_id, collect());
                        $subjects = $loads->pluck('subject.course_code')->filter()->unique()->sort()->implode(', ');
                        $hours = $loads->sum(fn($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject));

                        return [
                            'name' => $member->user?->usr_name ?? $member->full_name,
                            'subjects' => $subjects,
                            'hours' => $hours,
                        ];
                    })
                    ->values();

                return [
                    'department' => $department,
                    'college' => $department->department ?? $college,
                    'faculty' => $departmentFaculty,
                ];
            })
            ->values();

        $chairs = Dept_Chair::with('department')->get();

        $sectionQuery = Section::with(['program.department']);
        if ($collegeId) {
            $sectionQuery->whereHas('program', function ($query) use ($collegeId) {
                $query->where('dept_college_id', $collegeId);
            });
        }

        $sections = $sectionQuery
            ->get()
            ->sortBy(function ($section) use ($departmentSortKey) {
                return implode('|', [
                    $departmentSortKey($section->program),
                    strtolower((string) ($section->sec_name ?? '')),
                    str_pad((string) ($section->sec_year_level ?? ''), 2, '0', STR_PAD_LEFT),
                ]);
            })
            ->values();

        $sectionsByDepartment = $departments
            ->map(function ($department) use ($sections, $college) {
                $departmentSections = $sections
                    ->filter(fn($section) => $section->program?->dept_id === $department->dept_id)
                    ->values();

                return [
                    'department' => $department,
                    'college' => $department->department ?? $college,
                    'sections' => $departmentSections,
                ];
            })
            ->values();

        return view('dean.schedule_reports', compact(
            'activeSemester',
            'college',
            'byDepartment',
            'byFaculty',
            'sectionsByDepartment',
            'chairs'
        ));
    }

    public function departmentDetail(string $deptId)
    {
        $activeSemester = Semester::where('sem_is_active', true)->first();

        $facultyRecords = Faculty::where('fac_college_id', $deptId)->get();
        $facultyIds = $facultyRecords->pluck('fac_id');

        $loadsByFaculty = Study_Load::with('subject')
            ->whereIn('sl_fac_id', $facultyIds)
            ->when($activeSemester, fn($q) => $q->where('sl_sem_id', $activeSemester->sem_id))
            ->get()
            ->groupBy('sl_fac_id');

        $faculty = $facultyRecords->map(function ($fac) use ($loadsByFaculty) {
            $hours = $loadsByFaculty->get($fac->fac_id, collect())
                ->sum(fn($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject));
            $maxHours = \App\Services\ScheduleAssignmentService::facultyMaxHours($fac);
            return [
                'name'       => $fac->full_name,
                'rank'       => $fac->fac_rank,
                'employment' => $fac->fac_employment_type,
                'load'       => $hours . 'h',
                'status'     => $hours > $maxHours ? 'Overload' : ($hours >= $maxHours - 3 ? 'Near Max' : 'OK'),
            ];
        });

        $sections = Section::whereHas('program', fn($q) => $q->where('dept_college_id', $deptId))->get();

        return response()->json([
            'faculty'  => $faculty,
            'sections' => $sections->count(),
        ]);
    }

    public function detectConflicts(string $sectionId = null)
    {
        $schedules = Schedule::with(['faculty', 'subject', 'room'])
            ->where('sch_is_active', true)
            ->when($sectionId, fn($q) => $q->where('sch_sec_id', $sectionId))
            ->get();

        $conflicts = [];
        $seen = [];

        foreach ($schedules->groupBy('sch_fac_id') as $group) {
            foreach ($group as $a) {
                foreach ($group as $b) {
                    if ($a->sch_id === $b->sch_id || $a->sch_day !== $b->sch_day) continue;

                    $overlap = $a->sch_start_time < $b->sch_end_time && $b->sch_start_time < $a->sch_end_time;
                    if (!$overlap) continue;

                    $pairKey = collect([$a->sch_id, $b->sch_id])->sort()->implode('-');
                    if (isset($seen[$pairKey])) continue;
                    $seen[$pairKey] = true;

                    $conflicts[] = [
                        'faculty'   => $a->faculty->full_name ?? 'Unknown',
                        'subject_a' => $a->subject->subj_code ?? '',
                        'subject_b' => $b->subject->subj_code ?? '',
                        'day'       => $a->sch_day,
                        'time'      => $a->sch_start_time . '–' . $a->sch_end_time,
                        'room_a'    => $a->room->room_name ?? '',
                        'room_b'    => $b->room->room_name ?? '',
                    ];
                }
            }
        }

        return response()->json($conflicts);
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'message'       => 'required|string',
            'type'          => 'required|in:info,reminder,urgent,deadline',
            'recipients'    => 'required|array|min:1',
            'recipients.*'  => 'exists:USER,usr_id',
        ]);

        foreach ($request->recipients as $usrId) {
            Notification::create([
                'notif_usr_id'  => $usrId,
                'notif_title'   => $request->title,
                'notif_message' => $request->message,
                'notif_type'    => $request->type,
                'notif_is_read' => false,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
