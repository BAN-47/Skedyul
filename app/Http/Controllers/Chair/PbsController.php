<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Course;
use App\Models\Departments;
use App\Services\ScheduleAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PbsController extends Controller
{
    public function __construct(private ScheduleAssignmentService $scheduler)
    {
    }

    /**
     * Resolve the logged-in department chair's assigned program.
     * Each program (BSIS, BSIT, BSCE, …) has its own chair.
     */
    private function chairProgramId(): ?string
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        $chair = Dept_Chair::where('dc_usr_id', $user->usr_id)->first();

        return $chair?->dc_dept_id;
    }

    /** Selected semester row, defaulting to the official active semester. */
    private function activeSemester(?string $selectedId = null): ?object
    {
        $semesterQuery = DB::table('semester as s')
            ->leftJoin('academic_year as ay', 'ay.ay_id', '=', 's.sem_ay_id')
            ->select('s.*', 'ay.ay_academic_year', 'ay.ay_year_label');
        $sem = $selectedId
            ? (clone $semesterQuery)->where('s.sem_id', $selectedId)->first()
            : null;
        $sem ??= (clone $semesterQuery)->where('s.sem_is_active', true)->first();
        $sem ??= (clone $semesterQuery)->orderByDesc('s.sem_start_date')->first();
        if (!$sem) {
            return null;
        }

        $year = $sem->ay_academic_year ?? $sem->ay_year_label ?? null;
        if (!$year) {
            $activeAy = DB::table('academic_year')->where('ay_is_active', true)->first();
            $year = $activeAy->ay_academic_year ?? $activeAy->ay_year_label ?? '';
        }
        $semName = $sem->sem_name ?? '';
        $yearDisp = $year !== '' ? str_replace('-', ' - ', $year) : '';
        $sem->label = trim($semName . ($yearDisp !== '' ? ', AY ' . $yearDisp : ''));
        $sem->ay_academic_year = $year;
        return $sem;
    }

    /**
     * PBS page — scoped to the chair's program only.
     * BSIS chair sees only BSIS programs/sections (BSIS 1-A, BSIS 2-B, …).
     * Semester defaults to the official active semester, or the latest term if none is active.
     */
    public function index(Request $request)
    {
        $progId = $this->chairProgramId();
        $dept = Departments::find($progId);
        $collegeId = $dept?->dept_college_id;
        $allDepartments = $collegeId ? Departments::where('dept_college_id', $collegeId)->orderBy('dept_code')->get() : collect();

        // Default to logged-in chair's department unless they select another one.
        $selectedDeptId = $request->query('department', $progId);

        if (!$progId) {
            return response()->view('chair.pbs', [
                'schedules'        => collect(),
                'subjects'         => collect(),
                'faculty'          => collect(),
                'sections'         => collect(),
                'programs'         => collect(),
                'departments'      => collect(),
                'academicYears'    => collect(),
                'semesters'        => collect(),
                'rooms'            => collect(),
                'filters'          => [],
                'selectedDate'     => $request->query('date'),
                'chairProgram'     => null,
                'chairDepartment'  => null,
                'activeSemester'   => $this->activeSemester(),
                'selectedSection'  => null,
                'signoff'          => app(\App\Services\ScheduleSignoffService::class)->names(),
                'error'            => 'Your account is not linked to a department. Contact the system administrator.',
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache');
        }

        $filters = $request->only(['section']);
        $filters['department'] = $selectedDeptId;

        $activeSem = $this->activeSemester($request->query('semester'));
        $filters['semester'] = $activeSem->sem_id ?? null;
        $shiftKey = $request->query('shift', 'day') === 'night' ? 'evening' : 'day';

        // Sections for all departments in college (so department switcher in modal works)
        $sections = Section::query()
            ->whereIn('sec_dept_id', $allDepartments->pluck('dept_id'))
            ->orderBy('sec_name')
            ->get();

        $schedules = collect();
        if (!empty($filters['section'])) {
            $sectionOk = $sections->contains('sec_id', $filters['section']);
            if ($sectionOk) {
                $schedules = Schedule::query()
                    ->where('sch_sec_id', $filters['section'])
                    ->when(
                        !empty($filters['semester']),
                        fn ($q) => $q->where('sch_sem_id', $filters['semester']),
                        fn ($q) => $q->whereRaw('1 = 0')
                    )
                    ->where('sch_is_active', true)
                    ->when(
                        $shiftKey === 'evening',
                        fn ($q) => $q->whereRaw("sch_start_time::time < TIME '22:00' AND sch_end_time::time > TIME '16:00'"),
                        fn ($q) => $q->whereRaw("sch_start_time::time < TIME '16:00' AND sch_end_time::time > TIME '07:00'")
                    )
                    ->with(['subject', 'faculty', 'section', 'room', 'semester'])
                    ->orderBy('sch_day')
                    ->orderBy('sch_start_time')
                    ->get();
            }
        }

        $programs = Departments::where('dept_id', $progId)->get();

        $selectedSection = null;
        if (!empty($filters['section'])) {
            $selectedSection = $sections->firstWhere('sec_id', $filters['section']);
        }

        $summaryShift = $selectedSection?->sec_is_evening ? 'evening' : $shiftKey;
        $summarySchedules = $schedules;
        if ($selectedSection?->sec_is_evening) {
            $summarySchedules = Schedule::query()
                ->where('sch_sec_id', $selectedSection->sec_id)
                ->when(
                    !empty($filters['semester']),
                    fn ($query) => $query->where('sch_sem_id', $filters['semester']),
                    fn ($query) => $query->whereRaw('1 = 0')
                )
                ->where('sch_is_active', true)
                ->with(['subject', 'faculty', 'section', 'room', 'semester'])
                ->orderBy('sch_day')
                ->orderBy('sch_start_time')
                ->get();
        }

        // Courses for this department, filtered by section year + active semester + category
        // course_year_level: 1–4 | course_semester: 1 = 1st sem, 2 = 2nd sem
        $selectedCategory = $request->query('category');

        $subjectDeptId = $selectedDeptId ?: $progId;
        $subjectsQuery = Course::query()
            ->where('course_is_active', true)
            ->orderBy('course_code');

        if (!empty($selectedCategory)) {
            $subjectsQuery->where('course_dept_id', $progId)
                ->where('course_category', $selectedCategory);
        } else {
            // No category: filter by selected department + year level + semester
            $subjectsQuery->where('course_dept_id', $subjectDeptId);

            if (!empty($selectedSection?->sec_year_level)) {
                $subjectsQuery->where('course_year_level', (int) $selectedSection->sec_year_level);
            }

            $semNum = null;
            $semName = strtolower((string) ($activeSem->sem_name ?? ''));
            if (str_contains($semName, '2nd') || str_contains($semName, 'second')) {
                $semNum = 2;
            } elseif (str_contains($semName, '1st') || str_contains($semName, 'first')) {
                $semNum = 1;
            }
            if ($semNum !== null) {
                $subjectsQuery->where('course_semester', $semNum);
            }
        }

        $subjects = $subjectsQuery->get();

        // Get ALL unique categories in DB (global, no semester filter)
        $categories = Course::where('course_is_active', true)
            ->whereNotNull('course_category')
            ->where('course_category', '!=', '')
            ->distinct()
            ->orderBy('course_category')
            ->pluck('course_category');

        $prog = $programs->first();
        // All teachers who can be assigned (faculty table only — needed for FK).
        // Includes full-time, part-time, dept chairs, and deans who have a faculty row.
        $faculty = Faculty::query()
            ->whereHas('user', fn ($query) => $query->where('usr_is_active', true))
            ->orderBy('fac_last_name')
            ->orderBy('fac_first_name')
            ->get();

        return response()->view('chair.pbs', [
            'schedules'        => $schedules,
            'subjects'         => $subjects,
            'allSubjects'      => Course::where('course_is_active', true)->orderBy('course_code')->get(),
            'faculty'          => $faculty,
            'sections'         => $sections,
            'programs'         => $allDepartments,
            'departments'      => $allDepartments,
            'allDepartments'   => $allDepartments,
            'selectedDeptId'   => $selectedDeptId,
            'categories'       => $categories,
            'selectedCategory' => $selectedCategory,
            'academicYears' => AcademicYear::query()->orderByDesc('ay_academic_year')->get(),
            // Labels like "2026-2027 1st Sem"
            'semesters'     => DB::table('semester as s')
                ->leftJoin('academic_year as ay', 'ay.ay_id', '=', 's.sem_ay_id')
                ->orderByDesc('s.sem_start_date')
                ->select([
                    's.sem_id',
                    's.sem_name',
                    's.sem_is_active',
                    's.sem_start_date',
                    's.sem_ay_id',
                    'ay.ay_academic_year',
                    'ay.ay_year_label',
                ])
                ->get()
                ->map(function ($sem) {
                    $ay = $sem->ay_year_label ?? $sem->ay_academic_year ?? '';
                    $ayDisp = $ay !== '' ? str_replace('-', ' - ', $ay) : '';
                    $label = trim(($sem->sem_name ?? '') . ($ayDisp !== '' ? ', AY ' . $ayDisp : ''));
                    if (!empty($sem->sem_is_active)) {
                        $label .= ' (Current)';
                    }
                    $sem->label = $label !== '' ? $label : $sem->sem_name;
                    return $sem;
                }),
            'rooms'         => DB::table('room')->where('room_is_available', true)->orderBy('room_name')->get(),
            'filters'       => $filters,
            'selectedDate'  => $request->query('date'),
            'chairProgram'     => $allDepartments->first(),
            'chairDepartment'  => $allDepartments->first(),
            'activeSemester'   => $activeSem,
            'selectedSection'  => $selectedSection,
            'selectedShift'    => $shiftKey,
            'summaryShift'     => $summaryShift,
            'summarySchedules' => $summarySchedules,
            'signoff'          => app(\App\Services\ScheduleSignoffService::class)->names(),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    private function validated(Request $request, bool $multipleDays = false): array
    {
        $data = $request->validate([
            'subj_id'    => 'required|uuid|exists:course,course_id',
            'fac_id'     => 'required|uuid|exists:faculty,fac_id',
            'sec_id'     => 'required|uuid|exists:section,sec_id',
            'room_id'    => 'required|uuid|exists:room,room_id',
            'sem_id'     => 'required|uuid|exists:semester,sem_id',
            'day'        => $multipleDays ? 'prohibited' : 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'shift'      => 'required|string|in:day,evening',
            'days'       => $multipleDays ? 'required|array|min:1' : 'prohibited',
            'days.*'     => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => ['required', 'date_format:H:i', 'regex:/^\d{2}:[03]0$/', 'after_or_equal:07:00', 'before:22:00'],
            'end_time'   => ['required', 'date_format:H:i', 'regex:/^\d{2}:[03]0$/', 'after:start_time', 'before_or_equal:22:00'],
        ]);

        $progId = $this->chairProgramId();
        if ($progId) {
            $ownsSection = Section::where('sec_id', $data['sec_id'])
                ->where('sec_dept_id', $progId)
                ->exists();

            if (!$ownsSection) {
                abort(403, 'You can only schedule sections under your assigned program.');
            }
        }

        return $data;
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, true);
        $result = $this->scheduler->assignMultiple($data);

        return response()->json($result, $result['success'] ? 201 : 422);
    }

    public function update(Request $request, string $id)
    {
        $progId = $this->chairProgramId();
        if ($progId && !Schedule::whereKey($id)
            ->whereHas('section', fn ($query) => $query->where('sec_dept_id', $progId))
            ->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only update schedules under your assigned program.',
            ], 403);
        }

        $data = $this->validated($request);
        $schedule = Schedule::find($id);
        if (!$schedule || (string) $schedule->sch_sem_id !== (string) $data['sem_id']) {
            return response()->json([
                'success' => false,
                'message' => 'The schedule is not part of the selected semester.',
            ], 404);
        }
        $result = $this->scheduler->update($id, $data);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function destroy(Request $request, string $id)
    {
        $data = $request->validate([
            'sem_id' => 'required|uuid|exists:semester,sem_id',
        ]);
        $schedule = Schedule::find($id);
        if (!$schedule || (string) $schedule->sch_sem_id !== (string) $data['sem_id']) {
            return response()->json([
                'success' => false,
                'message' => 'The schedule is not part of the selected semester.',
            ], 404);
        }

        $progId = $this->chairProgramId();
        if ($progId) {
            $schedule = Schedule::with('section')->find($id);
            if ($schedule && $schedule->section && $schedule->section->sec_dept_id !== $progId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete schedules under your assigned program.',
                ], 403);
            }
        }

        return response()->json($this->scheduler->delete($id));
    }

    public function clear(Request $request)
    {
        $data = $request->validate([
            'section' => 'required|uuid|exists:section,sec_id',
            'semester' => 'required|uuid|exists:semester,sem_id',
        ]);
        $sectionId = $data['section'];

        $progId = $this->chairProgramId();
        if ($progId) {
            $owns = Section::where('sec_id', $sectionId)
                ->where('sec_dept_id', $progId)
                ->exists();
            if (!$owns) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only clear schedules under your assigned program.',
                ], 403);
            }
        }

        $query = Schedule::where('sch_sec_id', $sectionId)
            ->where('sch_sem_id', $data['semester']);

        $schedules = $query->get(['sch_id', 'sch_load_id', 'sch_fac_id', 'sch_sem_id']);
        $ids = $schedules->pluck('sch_load_id');

        Schedule::whereIn('sch_id', $schedules->pluck('sch_id'))->delete();
        \App\Models\Study_Load::whereIn('sl_id', $ids)->delete();
        $schedules->unique(fn ($schedule) => $schedule->sch_fac_id.'|'.$schedule->sch_sem_id)
            ->each(fn ($schedule) => $this->scheduler->syncWorkload($schedule->sch_fac_id, $schedule->sch_sem_id));

        return response()->json(['success' => true, 'message' => 'Cleared.']);
    }

    /**
     * Save number of students for the selected section (sec_no_of_student).
     * Chair may only update sections under their department.
     */
    public function updateStudents(Request $request)
    {
        $data = $request->validate([
            'sec_id'            => 'required|uuid|exists:section,sec_id',
            'sec_no_of_student' => 'required|integer|min:0|max:500',
        ]);

        $progId = $this->chairProgramId();
        $section = Section::where('sec_id', $data['sec_id'])->first();

        if (!$section) {
            return response()->json(['success' => false, 'message' => 'Section not found.'], 404);
        }

        if ($progId && $section->sec_dept_id !== $progId) {
            return response()->json([
                'success' => false,
                'message' => 'You can only update sections under your assigned department.',
            ], 403);
        }

        $section->sec_no_of_student = $data['sec_no_of_student'];
        $section->save();

        return response()->json([
            'success' => true,
            'message' => 'Number of students saved for ' . ($section->sec_name ?? 'section') . '.',
            'sec_no_of_student' => (int) $section->sec_no_of_student,
        ]);
    }

    /**
     * Fetch schedules for a department+section+shift (AJAX for PBS grid).
     */
    public function fetchSchedules(Request $request)
    {
        $progId = $this->chairProgramId();
        if (!$progId) {
            return response()->json(['success' => false, 'message' => 'Not linked to a department.'], 403);
        }

        $data = $request->validate([
            'department' => 'required|uuid|exists:department,dept_id',
            'section'    => 'required|uuid|exists:section,sec_id',
            'semester'   => 'required|uuid|exists:semester,sem_id',
            'shift'      => 'required|in:day,evening',
        ]);

        $dept = Departments::find($data['department']);
        $section = Section::find($data['section']);

        if (!$dept || !$section) {
            return response()->json(['success' => false, 'message' => 'Department or section not found.'], 404);
        }

        if ($section->sec_dept_id !== $dept->dept_id) {
            return response()->json(['success' => false, 'message' => 'Section does not belong to selected department.'], 403);
        }

        // Verify chair has access to this department's college
        $chair = \App\Models\Dept_Chair::where('dc_usr_id', auth()->id())->first();
        if ($chair && $chair->dc_college_id !== $dept->dept_college_id) {
            return response()->json(['success' => false, 'message' => 'Not authorized for this department.'], 403);
        }

        $activeSem = $this->activeSemester($data['semester']);
        if (!$activeSem) {
            return response()->json(['success' => false, 'message' => 'No active semester set.'], 422);
        }

        $shiftKey = $request->input('shift');

        $schedules = Schedule::query()
            ->where('sch_sec_id', $data['section'])
            ->when(
                $activeSem,
                fn ($q) => $q->where('sch_sem_id', $activeSem->sem_id),
                fn ($q) => $q->whereRaw('1 = 0')
            )
            ->where('sch_is_active', true)
            ->when(
                $shiftKey === 'evening',
                fn ($q) => $q->whereRaw("sch_start_time::time < TIME '22:00' AND sch_end_time::time > TIME '16:00'"),
                fn ($q) => $q->whereRaw("sch_start_time::time < TIME '16:00' AND sch_end_time::time > TIME '07:00'")
            )
            ->with(['subject', 'faculty', 'section', 'room', 'semester'])
            ->orderBy('sch_day')
            ->orderBy('sch_start_time')
            ->get()
            ->map(function ($sch) {
                return [
                    'id'           => $sch->sch_id,
                    'course_code'  => $sch->subject?->course_code ?? '',
                    'course_name'  => $sch->subject?->course_name ?? '',
                    'faculty_name' => $sch->faculty ? trim("{$sch->faculty->fac_first_name} {$sch->faculty->fac_last_name}") : '',
                    'room_name'    => $sch->room?->room_name ?? '',
                    'day'          => $sch->sch_day,
                    'start_time'   => $sch->sch_start_time ? substr($sch->sch_start_time, 0, 5) : '',
                    'end_time'     => $sch->sch_end_time ? substr($sch->sch_end_time, 0, 5) : '',
                    'shift'        => $sch->sch_shift,
                ];
            });

        return response()->json([
            'success'   => true,
            'schedules' => $schedules,
            'section'   => [
                'id'          => $section->sec_id,
                'name'        => $section->sec_name,
                'year_level'  => $section->sec_year_level,
            ],
        ]);
    }
}
