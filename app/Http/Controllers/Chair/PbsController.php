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

    /**
     * Current (active) semester id, if any.
     */
    private function currentSemesterId(): ?string
    {
        return Semester::where('sem_is_active', true)
            ->orderByDesc('sem_start_date')
            ->value('sem_id');
    }

    /** Active semester row + display label from Admin Settings (sem_is_active). */
    private function activeSemester(): ?object
    {
        // Semester table has only 1st/2nd rows; year comes from active academic_year
        $sem = DB::table('semester')->where('sem_is_active', true)->first();
        if (!$sem) {
            return null;
        }

        $ay = DB::table('academic_year')->where('ay_is_active', true)->first();
        $year = $ay->ay_academic_year ?? $ay->ay_year_label ?? '';
        $sem->label = trim($year . ($year !== '' ? ' · ' : '') . ($sem->sem_name ?? ''));
        $sem->ay_academic_year = $year;
        return $sem;
    }

    /**
     * PBS page — scoped to the chair's program only.
     * BSIS chair sees only BSIS programs/sections (BSIS 1-A, BSIS 2-B, …).
     * Semester defaults to the currently active semester.
     */
    public function index(Request $request)
    {
        $progId = $this->chairProgramId();

        if (!$progId) {
            return view('chair.pbs', [
                'schedules'     => collect(),
                'subjects'      => collect(),
                'faculty'       => collect(),
                'sections'      => collect(),
                'programs'      => collect(),
                'academicYears' => collect(),
                'semesters'     => collect(),
                'rooms'         => collect(),
                'filters'        => [],
                'selectedDate'   => $request->query('date'),
                'chairProgram'   => null,
                'activeSemester' => $this->activeSemester(),
                'error'          => 'Your account is not linked to a program. Contact the system administrator.',
            ]);
        }

        $filters = $request->only(['section']);

        // Program is locked to the chair's program — never from the query string
        $filters['program'] = $progId;

        // Semester is FIXED from Admin → Academic Year settings (not user-selectable)
        $activeSem = $this->activeSemester();
        $filters['semester'] = $activeSem->sem_id ?? null;

        // Sections for this chair's program only.
        // Year is already in the name (BSIS 1-A, BSIS IV-A) — no year-level filter.
        // Do not filter by sec_ay_id; null values would empty the dropdown.
        $sections = Section::query()
            ->where('sec_dept_id', $progId)
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
                        fn ($q) => $q->where('sch_sem_id', $filters['semester'])
                    )
                    ->where('sch_is_active', true)
                    ->with(['subject', 'faculty', 'section', 'room', 'semester'])
                    ->orderBy('sch_day')
                    ->orderBy('sch_start_time')
                    ->get();
            }
        }

        $programs = Departments::where('dept_id', $progId)->get();

        $subjects = Course::query()
            ->where('course_is_active', true)
            ->where('course_dept_id', $progId)
            ->orderBy('course_code')
            ->get();

        $prog = $programs->first();
        $faculty = Faculty::query()
            ->where(function ($q) use ($progId, $prog) {
                $q->where('fac_dept_id', $progId);
                if ($prog?->dept_college_id) {
                    $q->orWhere('fac_college_id', $prog->dept_college_id);
                }
            })
            ->orderBy('fac_last_name')
            ->orderBy('fac_first_name')
            ->get();

        return view('chair.pbs', [
            'schedules'     => $schedules,
            'subjects'      => $subjects,
            'faculty'       => $faculty,
            'sections'      => $sections,
            'programs'      => $programs,
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
                    $label = trim($ay . ($ay !== '' ? ' · ' : '') . $sem->sem_name);
                    if (!empty($sem->sem_is_active)) {
                        $label .= ' (Current)';
                    }
                    $sem->label = $label !== '' ? $label : $sem->sem_name;
                    return $sem;
                }),
            'rooms'         => DB::table('room')->where('room_is_available', true)->orderBy('room_name')->get(),
            'filters'       => $filters,
            'selectedDate'  => $request->query('date'),
            'chairProgram'  => $programs->first(),
            'activeSemester'=> $activeSem ?? $this->activeSemester(),
        ]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'subj_id'    => 'required|uuid|exists:course,course_id',
            'fac_id'     => 'required|uuid|exists:faculty,fac_id',
            'sec_id'     => 'required|uuid|exists:section,sec_id',
            'room_id'    => 'required|uuid|exists:room,room_id',
            'sem_id'     => 'required|uuid|exists:semester,sem_id',
            'day'        => 'required|string|max:15',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
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
        $data = $this->validated($request);
        $data['sem_id'] = $this->currentSemesterId() ?? ($data['sem_id'] ?? null);
        if (empty($data['sem_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'No active semester is set. Ask the admin to set Academic Year in Settings.',
            ], 422);
        }
        $result = $this->scheduler->assign($data);

        return response()->json($result, $result['success'] ? 201 : 422);
    }

    public function update(Request $request, string $id)
    {
        $data = $this->validated($request);
        $data['sem_id'] = $this->currentSemesterId() ?? ($data['sem_id'] ?? null);
        if (empty($data['sem_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'No active semester is set. Ask the admin to set Academic Year in Settings.',
            ], 422);
        }
        $result = $this->scheduler->update($id, $data);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function destroy(string $id)
    {
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

    public function saveDraft(Request $request)
    {
        return response()->json(['success' => true, 'message' => 'Draft saved.']);
    }

    public function clear(Request $request)
    {
        $sectionId = $request->input('section');
        if (!$sectionId) {
            return response()->json(['success' => false, 'message' => 'No section selected.']);
        }

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
            ->when(
                $request->filled('semester'),
                fn ($q) => $q->where('sch_sem_id', $request->input('semester'))
            );

        $ids = $query->pluck('sch_load_id', 'sch_id');

        Schedule::whereIn('sch_id', $ids->keys())->delete();
        \App\Models\Study_Load::whereIn('sl_id', $ids->values())->delete();

        return response()->json(['success' => true, 'message' => 'Cleared.']);
    }
}
