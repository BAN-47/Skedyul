<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\College;
use App\Models\Departments;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use App\Services\FacultyNameFormatter;
use App\Services\ScheduleAssignmentService;
use App\Models\Semester;
use App\Models\Study_Load;
use App\Models\WorkloadOverride;

class ReportsController extends Controller
{
    public function index()
    {
        $semester = Semester::where('sem_is_active', true)->first();
        $sectionSearch = trim((string) request('section_search', ''));
        $teacherSearch = trim((string) request('teacher_search', ''));
        $roomSearch = trim((string) request('room_search', ''));
        $sectionCollege = trim((string) request('section_college', ''));
        $sectionDepartment = trim((string) request('section_department', ''));

        $colleges = College::query()->orderBy('college_name')->get();
        $departments = Departments::query()
            ->when($sectionCollege !== '', fn ($query) => $query->where('dept_college_id', $sectionCollege))
            ->orderBy('dept_name')->get();
        $bySection = $this->getSectionData($semester, $sectionSearch, $sectionCollege, $sectionDepartment);
        $byTeacher = $this->getTeacherData($semester, $teacherSearch);
        $byRoom    = $this->getRoomData($semester, $roomSearch);
        $workloadOverrideCount = $byTeacher->sum(fn ($facultyReport) => $facultyReport->workload_overrides->count());

        return view('admin.reports', compact(
            'semester',
            'bySection',
            'colleges',
            'departments',
            'sectionCollege',
            'sectionDepartment',
            'byTeacher',
            'byRoom',
            'workloadOverrideCount'
        ));
    }

    public function facultySchedule(string $facultyId): JsonResponse
    {
        $semester = Semester::with('academicYear')->where('sem_is_active', true)->first();
        abort_if(!$semester, 404, 'There is no active semester.');

        $faculty = Faculty::with(['college', 'program'])
            ->where('fac_is_placeholder', false)
            ->findOrFail($facultyId);

        $schedules = Schedule::with(['subject', 'section', 'room'])
            ->where('sch_fac_id', $faculty->fac_id)
            ->where('sch_sem_id', $semester->sem_id)
            ->where('sch_is_active', true)
            ->orderByRaw("CASE UPPER(sch_day)
                WHEN 'MONDAY' THEN 1 WHEN 'MON' THEN 1
                WHEN 'TUESDAY' THEN 2 WHEN 'TUE' THEN 2
                WHEN 'WEDNESDAY' THEN 3 WHEN 'WED' THEN 3
                WHEN 'THURSDAY' THEN 4 WHEN 'THU' THEN 4
                WHEN 'FRIDAY' THEN 5 WHEN 'FRI' THEN 5
                WHEN 'SATURDAY' THEN 6 WHEN 'SAT' THEN 6
                WHEN 'SUNDAY' THEN 7 WHEN 'SUN' THEN 7
                ELSE 8 END")
            ->orderBy('sch_start_time')
            ->get();

        $load = $this->facultyLoadSummary($faculty, $semester);

        return response()->json([
            'faculty' => FacultyNameFormatter::format($faculty),
            'department' => $faculty->program?->dept_code ?? $faculty->program?->dept_name ?? 'Department not set',
            'semester' => $semester->sem_name . ($semester->academicYear ? ' · AY ' . ($semester->academicYear->ay_year_label ?? $semester->academicYear->ay_academic_year) : ''),
            'load' => $load,
            'schedules' => $schedules->map(fn (Schedule $schedule) => [
                'day' => $schedule->sch_day,
                'time' => $schedule->sch_start_time && $schedule->sch_end_time
                    ? Carbon::parse($schedule->sch_start_time)->format('g:i A') . ' – ' . Carbon::parse($schedule->sch_end_time)->format('g:i A')
                    : 'Time not set',
                'course_code' => $schedule->subject?->course_code ?? '—',
                'course_name' => $schedule->subject?->course_name ?? '',
                'section' => $schedule->section?->sec_name ?? '—',
                'room' => $schedule->room?->room_name ?? '—',
            ])->values(),
        ]);
    }

    public function sectionSchedule(string $sectionId): JsonResponse
    {
        $semester = Semester::with('academicYear')->where('sem_is_active', true)->first();
        abort_if(!$semester, 404, 'There is no active semester.');
        $section = Section::with('program')->findOrFail($sectionId);
        $schedules = $this->currentSchedules($semester, 'sch_sec_id', $section->sec_id);

        return response()->json([
            'title' => $section->sec_name . ' — Timetable',
            'subtitle' => ($section->program?->dept_code ?? 'Department not set') . ' · Year ' . ($section->sec_year_level ?? '—') . ' · ' . $this->semesterLabel($semester),
            'stats' => [
                ['label' => 'Scheduled classes', 'value' => $schedules->count()],
                ['label' => 'Subjects', 'value' => $schedules->pluck('course_code')->filter()->unique()->count()],
                ['label' => 'Rooms used', 'value' => $schedules->pluck('room')->filter(fn ($room) => $room !== '—')->unique()->count()],
            ],
            'schedules' => $schedules,
        ]);
    }

    public function roomSchedule(string $roomId): JsonResponse
    {
        $semester = Semester::with('academicYear')->where('sem_is_active', true)->first();
        abort_if(!$semester, 404, 'There is no active semester.');
        $room = Room::findOrFail($roomId);
        $schedules = $this->currentSchedules($semester, 'sch_room_id', $room->room_id);

        return response()->json([
            'title' => $room->room_name . ' — Timetable',
            'subtitle' => ucfirst($room->room_type ?? 'Room') . ' · Capacity ' . ($room->room_capacity ?? '—') . ' · ' . $this->semesterLabel($semester),
            'stats' => [
                ['label' => 'Scheduled classes', 'value' => $schedules->count()],
                ['label' => 'Sections using room', 'value' => $schedules->pluck('section')->filter(fn ($section) => $section !== '—')->unique()->count()],
                ['label' => 'Faculty assigned', 'value' => $schedules->pluck('faculty')->filter(fn ($faculty) => $faculty !== 'Unassigned')->unique()->count()],
            ],
            'schedules' => $schedules,
        ]);
    }

    private function semesterLabel(Semester $semester): string
    {
        return $semester->sem_name . ($semester->academicYear ? ' · AY ' . ($semester->academicYear->ay_year_label ?? $semester->academicYear->ay_academic_year) : '');
    }

    private function currentSchedules(Semester $semester, string $column, string $id)
    {
        $relationColumn = match ($column) {
            'sch_sec_id' => 'sl_sec_id',
            default => null,
        };

        return Schedule::with(['subject', 'faculty.user', 'section', 'room', 'studyLoad.subject', 'studyLoad.section'])
            ->where(function ($query) use ($column, $relationColumn, $id) {
                $query->where($column, $id);
                if ($relationColumn) {
                    $query->orWhereHas('studyLoad', fn ($load) => $load->where($relationColumn, $id));
                }
            })
            ->where('sch_sem_id', $semester->sem_id)
            ->where('sch_is_active', true)
            ->orderByRaw("CASE UPPER(sch_day)
                WHEN 'MONDAY' THEN 1 WHEN 'MON' THEN 1 WHEN 'TUESDAY' THEN 2 WHEN 'TUE' THEN 2
                WHEN 'WEDNESDAY' THEN 3 WHEN 'WED' THEN 3 WHEN 'THURSDAY' THEN 4 WHEN 'THU' THEN 4
                WHEN 'FRIDAY' THEN 5 WHEN 'FRI' THEN 5 WHEN 'SATURDAY' THEN 6 WHEN 'SAT' THEN 6
                WHEN 'SUNDAY' THEN 7 WHEN 'SUN' THEN 7 ELSE 8 END")
            ->orderBy('sch_start_time')
            ->get()
            ->map(function (Schedule $schedule) {
                $subject = $schedule->subject ?? $schedule->studyLoad?->subject;
                $section = $schedule->section ?? $schedule->studyLoad?->section;
                $room = $schedule->room;
                return [
                    'day' => $schedule->sch_day ?: '—',
                    'time' => $schedule->sch_start_time && $schedule->sch_end_time
                        ? Carbon::parse($schedule->sch_start_time)->format('g:i A') . ' – ' . Carbon::parse($schedule->sch_end_time)->format('g:i A')
                        : 'Time not set',
                    'course_code' => $subject?->course_code ?? '—',
                    'course_name' => $subject?->course_name ?? '',
                    'section' => $section?->sec_name ?? '—',
                    'room' => $room?->room_name ?? '—',
                    'faculty' => $schedule->faculty && !$schedule->faculty->fac_is_placeholder
                        ? (FacultyNameFormatter::format($schedule->faculty) ?: 'Faculty member')
                        : 'Unassigned',
                ];
            })->values();
    }

    // ── BY SECTION ───────────────────────────────────────────────────────
    private function getSectionData($semester, string $search = '', string $collegeId = '', string $departmentId = '')
    {
        $sectionsQuery = Section::with('program');
        if ($collegeId !== '') {
            $sectionsQuery->whereHas('program', fn ($department) => $department->where('dept_college_id', $collegeId));
        }
        if ($departmentId !== '') {
            $sectionsQuery->where('sec_dept_id', $departmentId);
        }
        if ($search !== '') {
            $pattern = '%' . mb_strtolower($search) . '%';
            $sectionsQuery->where(function ($query) use ($pattern) {
                $query->whereRaw('LOWER(sec_name) LIKE ?', [$pattern])
                    ->orWhereRaw('CAST(sec_year_level AS VARCHAR) LIKE ?', [$pattern])
                    ->orWhereHas('program', fn ($program) => $program->whereRaw('LOWER(dept_code) LIKE ?', [$pattern])->orWhereRaw('LOWER(dept_name) LIKE ?', [$pattern]));
            });
        }
        $sections = $sectionsQuery->orderBy('sec_name')->paginate(10, ['*'], 'section_page')->withQueryString();
        $sectionIds = $sections->pluck('sec_id');
        $schedules = $semester && $sections->isNotEmpty()
            ? Schedule::with(['subject', 'faculty.user', 'section', 'room', 'studyLoad.subject', 'studyLoad.section'])
                ->where(function ($query) use ($sectionIds) {
                    $query->whereIn('sch_sec_id', $sectionIds)
                        ->orWhereHas('studyLoad', fn ($load) => $load->whereIn('sl_sec_id', $sectionIds));
                })
                ->where('sch_sem_id', $semester->sem_id)->where('sch_is_active', true)
                ->orderBy('sch_start_time')->get()->groupBy(fn (Schedule $schedule) => $schedule->section?->sec_id ?? $schedule->studyLoad?->sl_sec_id)
            : collect();

        $sections->getCollection()->transform(function (Section $section) use ($schedules) {
            $section->reportSchedules = $schedules->get($section->sec_id, collect())->map(function (Schedule $schedule) {
                $subject = $schedule->subject ?? $schedule->studyLoad?->subject;
                $room = $schedule->room;
                return (object) [
                    'subj_code' => $subject?->course_code,
                    'subj_name' => $subject?->course_name,
                    'faculty_name' => $schedule->faculty && !$schedule->faculty->fac_is_placeholder ? (FacultyNameFormatter::format($schedule->faculty) ?: 'Faculty member') : 'Unassigned',
                    'day' => $schedule->sch_day,
                    'start_time' => $schedule->sch_start_time,
                    'end_time' => $schedule->sch_end_time,
                    'room_name' => $room?->room_name,
                ];
            });
            return $section;
        });

        return $sections;
    }

    // ── BY TEACHER ───────────────────────────────────────────────────────
    private function getTeacherData($semester, string $search = '')
    {
        $facultyQuery = Faculty::with(['user', 'college', 'program'])
            ->where('fac_is_placeholder', false);
        if ($search !== '') {
            $pattern = '%' . mb_strtolower($search) . '%';
            $facultyQuery->where(function ($query) use ($pattern) {
                foreach (['fac_first_name', 'fac_middle_name', 'fac_last_name', 'fac_employee_id'] as $column) {
                    $query->orWhereRaw("LOWER(COALESCE(CAST({$column} AS VARCHAR), '')) LIKE ?", [$pattern]);
                }
                $query->orWhereHas('program', fn ($program) => $program->whereRaw('LOWER(dept_code) LIKE ?', [$pattern])->orWhereRaw('LOWER(dept_name) LIKE ?', [$pattern]))
                    ->orWhereHas('college', fn ($college) => $college->whereRaw('LOWER(college_code) LIKE ?', [$pattern])->orWhereRaw('LOWER(college_name) LIKE ?', [$pattern]));
            });
        }
        $faculty = $facultyQuery->orderBy('fac_last_name')->orderBy('fac_first_name')->get();

        $loadsByFaculty = collect();
        if ($semester && $faculty->isNotEmpty()) {
            $loadsByFaculty = Study_Load::with(['subject', 'section'])
                ->where('sl_sem_id', $semester->sem_id)
                ->whereIn('sl_fac_id', $faculty->pluck('fac_id'))
                ->get()
                ->groupBy('sl_fac_id');
        }

        $overridesByFaculty = $semester && $faculty->isNotEmpty()
            ? WorkloadOverride::with(['approvedBy', 'course', 'section'])
                ->where('wlo_semester_id', $semester->sem_id)
                ->whereIn('wlo_fac_id', $faculty->pluck('fac_id'))
                ->orderByDesc('created_at')
                ->get()
                ->groupBy('wlo_fac_id')
            : collect();

        return $faculty->map(function (Faculty $member) use ($semester, $loadsByFaculty, $overridesByFaculty) {
            $loads = $loadsByFaculty->get($member->fac_id, collect());
            $workloadOverrides = $overridesByFaculty->get($member->fac_id, collect());
            $summary = $semester ? $this->facultyLoadSummary($member, $semester, $loads) : [
                'day_hours' => 0,
                'evening_hours' => 0,
                'total_hours' => 0,
                'day_cap' => ScheduleAssignmentService::facultyMaxHours($member, 'day'),
                'evening_cap' => ScheduleAssignmentService::facultyMaxHours($member, 'evening'),
                'part_time_cap' => $member->fac_employment_type === 'part_time' ? ScheduleAssignmentService::PART_TIME_MAX_HOURS : null,
                'is_part_time' => $member->fac_employment_type === 'part_time',
                'status' => 'No active semester',
            ];

            return (object) [
                'fac_id' => $member->fac_id,
                'name' => FacultyNameFormatter::format($member) ?: 'Faculty member',
                'college' => $member->college?->college_code ?? 'College not set',
                'department' => $member->program?->dept_code ?? $member->program?->dept_name ?? 'Department not set',
                'employment' => $member->fac_employment_type ?: '—',
                'designation' => $member->fac_special_position ?: $member->fac_rank ?: 'Faculty',
                'load' => $summary,
                'workload_overrides' => $workloadOverrides,
            ];
        });
    }

    private function facultyLoadSummary(Faculty $faculty, Semester $semester, $loads = null): array
    {
        $loads ??= Study_Load::with(['subject', 'section'])
            ->where('sl_fac_id', $faculty->fac_id)
            ->where('sl_sem_id', $semester->sem_id)
            ->get();

        $dayHours = 0.0;
        $eveningHours = 0.0;
        $countedOjtCourses = [];
        foreach ($loads as $load) {
            $course = $load->subject;
            if (!$course) {
                continue;
            }
            if (ScheduleAssignmentService::isOjt($course)) {
                $courseId = (string) $course->course_id;
                if (isset($countedOjtCourses[$courseId])) {
                    continue;
                }
                $countedOjtCourses[$courseId] = true;
            }

            $hours = ScheduleAssignmentService::courseHours($course);
            $shift = $load->section?->sec_is_evening
                ? 'evening'
                : ($load->sl_shift === 'evening' ? 'evening' : 'day');
            if ($shift === 'evening') {
                $eveningHours += $hours;
            } else {
                $dayHours += $hours;
            }
        }

        $dayCap = ScheduleAssignmentService::facultyMaxHours($faculty, 'day');
        $eveningCap = ScheduleAssignmentService::facultyMaxHours($faculty, 'evening');
        $totalHours = $dayHours + $eveningHours;
        $isPartTime = $faculty->fac_employment_type === 'part_time';
        $overLimit = $isPartTime
            ? $totalHours > ScheduleAssignmentService::PART_TIME_MAX_HOURS
            : ($dayHours > $dayCap || $eveningHours > $eveningCap);
        $atLimit = $isPartTime
            ? $totalHours >= ScheduleAssignmentService::PART_TIME_MAX_HOURS
            : ($dayHours >= $dayCap || $eveningHours >= $eveningCap);

        return [
            'day_hours' => round($dayHours, 1),
            'evening_hours' => round($eveningHours, 1),
            'total_hours' => round($totalHours, 1),
            'day_cap' => $dayCap,
            'evening_cap' => $eveningCap,
            'part_time_cap' => $isPartTime ? ScheduleAssignmentService::PART_TIME_MAX_HOURS : null,
            'is_part_time' => $isPartTime,
            'status' => $overLimit ? 'Over limit' : ($atLimit ? 'At limit' : ($totalHours <= 0 ? 'No load' : 'Within limit')),
        ];
    }

    // ── BY ROOM ──────────────────────────────────────────────────────────
    private function getRoomData($semester, string $search = '')
    {
        $roomQuery = Room::query();
        if ($search !== '') {
            $pattern = '%' . mb_strtolower($search) . '%';
            $roomQuery->where(function ($query) use ($pattern) {
                $query->whereRaw('LOWER(room_name) LIKE ?', [$pattern])
                    ->orWhereRaw("LOWER(COALESCE(room_type, '')) LIKE ?", [$pattern])
                    ->orWhereRaw("LOWER(COALESCE(room_building, '')) LIKE ?", [$pattern]);
            });
        }
        $rooms = $roomQuery->orderBy('room_name')->paginate(10, ['*'], 'room_page')->withQueryString();
        $roomIds = $rooms->pluck('room_id');
        $schedules = $semester && $rooms->isNotEmpty()
            ? Schedule::with(['subject', 'faculty.user', 'section', 'room', 'studyLoad.subject', 'studyLoad.section'])
                ->where(function ($query) use ($roomIds) {
                    $query->whereIn('sch_room_id', $roomIds);
                })
                ->where('sch_sem_id', $semester->sem_id)->where('sch_is_active', true)
                ->orderBy('sch_start_time')->get()->groupBy(fn (Schedule $schedule) => $schedule->sch_room_id)
            : collect();

        $rooms->getCollection()->transform(function (Room $room) use ($schedules) {
            $room->reportSchedules = $schedules->get($room->room_id, collect())->map(function (Schedule $schedule) {
                $subject = $schedule->subject ?? $schedule->studyLoad?->subject;
                $section = $schedule->section ?? $schedule->studyLoad?->section;
                return (object) [
                    'day' => $schedule->sch_day,
                    'start_time' => $schedule->sch_start_time,
                    'end_time' => $schedule->sch_end_time,
                    'subj_code' => $subject?->course_code,
                    'subj_name' => $subject?->course_name,
                    'sec_name' => $section?->sec_name,
                    'faculty_name' => $schedule->faculty && !$schedule->faculty->fac_is_placeholder ? (FacultyNameFormatter::format($schedule->faculty) ?: 'Faculty member') : 'Unassigned',
                ];
            });
            return $room;
        });

        return $rooms;
    }
}
