<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dean;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Study_Load;
use App\Models\Course;
use App\Models\Departments;
use App\Services\ScheduleAssignmentService;
use Illuminate\Http\Request;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

class PbtController extends Controller
{
    public function __construct(private ScheduleAssignmentService $scheduler)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['program']);

        // Same as PBS: chair only sees their department (BSIS chair → BSIS only)
        $deptId = \App\Models\Dept_Chair::where('dc_usr_id', auth()->user()?->usr_id)
            ->value('dc_dept_id');

        $activeSem = DB::table('semester')->where('sem_is_active', true)->first();
        $activeAy = DB::table('academic_year')->where('ay_is_active', true)->first();
        if ($activeSem) {
            $year = $activeAy->ay_academic_year ?? $activeAy->ay_year_label ?? '';
            $yearDisp = $year !== '' ? str_replace('-', ' - ', $year) : '';
            $activeSem->label = trim(($activeSem->sem_name ?? '') . ($yearDisp !== '' ? ', AY ' . $yearDisp : ''));
        }
        $filters['semester'] = $activeSem->sem_id ?? null;
        $facultyId = $request->query('faculty');

        $selectedFaculty = $facultyId
            ? Faculty::whereKey($facultyId)
                ->whereHas('user', fn ($query) => $query->where('usr_is_active', true))
                ->first()
            : null;

        $schedules = collect();
        if ($selectedFaculty) {
            $schedules = Schedule::query()
                ->where('sch_fac_id', $selectedFaculty->fac_id)
                ->when(!empty($filters['semester']), fn ($q) => $q->where('sch_sem_id', $filters['semester']))
                ->where('sch_is_active', true)
                ->with(['subject', 'section', 'room'])
                ->get();
        }

        $semName = strtolower((string) ($activeSem->sem_name ?? ''));
        $semNum = null;
        if (str_contains($semName, '2nd') || str_contains($semName, 'second')) {
            $semNum = 2;
        } elseif (str_contains($semName, '1st') || str_contains($semName, 'first')) {
            $semNum = 1;
        }

        $chairDepartment = $deptId ? Departments::where('dept_id', $deptId)->first() : null;
        $deptCollection = $chairDepartment
            ? collect([$chairDepartment])
            : collect();

        // Build teacher groups (must be faculty rows for FK)
        $allFaculty = Faculty::query()
            ->whereHas('user', fn ($query) => $query->where('usr_is_active', true))
            ->orderBy('fac_last_name')
            ->orderBy('fac_first_name')
            ->get();

        $chairUsrIds = \App\Models\Dept_Chair::pluck('dc_usr_id')->filter()->all();
        $deanUsrIds  = Dean::pluck('dean_usr_id')->filter()->all();

        // Every active department chair with a Faculty row can be selected.
        // Schedules store fac_id, so chairs/deans without a Faculty row cannot
        // be scheduled as teachers until that linked row exists.
        $facultyChairs = $allFaculty->filter(
            fn ($f) => in_array($f->fac_usr_id, $chairUsrIds, true)
        )->values();

        $deans = $allFaculty->filter(
            fn ($f) => in_array($f->fac_usr_id, $deanUsrIds, true)
        )->values();

        // Keep chairs and deans in their own groups so every eligible person
        // is visible without listing the same person in multiple groups.
        $otherChairIds = $allFaculty
            ->filter(fn ($f) => in_array($f->fac_usr_id, $chairUsrIds, true))
            ->pluck('fac_id')
            ->all();

        $specialIds = collect($otherChairIds)
            ->merge($deans->pluck('fac_id'))
            ->merge($facultyChairs->pluck('fac_id'))
            ->unique()
            ->all();

        $facultyFullTime = $allFaculty->filter(function ($f) use ($specialIds) {
            if (in_array($f->fac_id, $specialIds, true)) {
                return false;
            }
            $t = strtolower(str_replace([' ', '-'], '_', (string) ($f->fac_employment_type ?? '')));
            return str_contains($t, 'full') || $t === '' || !str_contains($t, 'part');
        })->values();

        $facultyPartTime = $allFaculty->filter(function ($f) use ($specialIds) {
            if (in_array($f->fac_id, $specialIds, true)) {
                return false;
            }
            $t = strtolower(str_replace([' ', '-'], '_', (string) ($f->fac_employment_type ?? '')));
            return str_contains($t, 'part');
        })->values();

        return view('chair.pbt', [
            'schedules'        => $schedules,
            'subjects'         => Course::where('course_is_active', true)
                ->when($deptId, fn ($q) => $q->where('course_dept_id', $deptId))
                ->when($semNum !== null, fn ($q) => $q->where('course_semester', $semNum))
                ->orderBy('course_code')
                ->get(),
            'facultyFullTime'  => $facultyFullTime,
            'facultyPartTime'  => $facultyPartTime,
            'facultyChairs'    => $facultyChairs,
            'deans'            => $deans,

            // BSIS chair → only BSIS sections; BSIT chair → only BSIT sections
            'sections'         => DB::table('section')
                ->when($deptId, fn ($q) => $q->where('sec_dept_id', $deptId))
                ->orderBy('sec_name')
                ->get(),
            'programs'         => $deptCollection,
            'departments'      => $deptCollection,
            'chairDepartment'  => $chairDepartment,
            'semesters'        => DB::table('semester as s')
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
            'rooms'            => DB::table('room')->where('room_is_available', true)->orderBy('room_name')->get(),
            'filters'          => $filters,
            'selectedFaculty'  => $selectedFaculty,
            'selectedDate'     => $request->query('date'),
            'loadStats'        => $this->loadStatsFor($selectedFaculty, $filters['semester'] ?? null),
            'activeSemester'   => $activeSem ?? null,
        ]);
    }

    private function loadStatsFor(?Faculty $faculty, ?string $semId): array
    {
        if (!$faculty) {
            return [
                'preparations' => null, 'units' => null, 'hours_week' => null,
                'designation' => null, 'production' => null, 'extension' => null, 'research' => null,
            ];
        }

        $loads = Study_Load::where('sl_fac_id', $faculty->fac_id)
            ->when($semId, fn ($q) => $q->where('sl_sem_id', $semId))
            ->with('subject', 'schedules')
            ->get();

        // Academic credit units and scheduled weekly contact hours are
        // different totals. Units come from each distinct Study Load course.
        $units = $loads->sum(fn ($load) => (float) ($load->subject?->course_units ?? 0));

        $hoursPerWeek = $loads->sum(function ($load) {
            return $load->schedules->sum(function ($schedule) {
                $start = \Illuminate\Support\Carbon::parse($schedule->sch_start_time);
                $end = \Illuminate\Support\Carbon::parse($schedule->sch_end_time);
                return $start->diffInMinutes($end) / 60;
            });
        });

        return [
            'preparations' => $loads->pluck('sl_course_id')->unique()->count(),
            'units'        => $units ?: null,
            'hours_week'   => $hoursPerWeek ?: null,
            'designation'  => $faculty->fac_rank,
            'production'   => null,
            'extension'    => null,
            'research'     => null,
        ];
    }


    private function currentSemesterId(): ?string
    {
        return \App\Models\Semester::where('sem_is_active', true)
            ->orderByDesc('sem_start_date')
            ->value('sem_id');
    }

    private function validated(Request $request, bool $multipleDays = false): array
    {
        return $request->validate([
            'subj_id'    => 'required|uuid|exists:course,course_id',
            'fac_id'     => 'required|uuid|exists:faculty,fac_id',
            'sem_id'     => 'nullable|uuid|exists:semester,sem_id',
            'sec_id'     => 'required|uuid|exists:section,sec_id',
            'room_id'    => 'required|uuid|exists:room,room_id',
            'day'        => $multipleDays ? 'prohibited' : 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'days'       => $multipleDays ? 'required|array|min:1' : 'prohibited',
            'days.*'     => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, true);
        $data['sem_id'] = $this->currentSemesterId() ?? ($data['sem_id'] ?? null);
        if (empty($data['sem_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'No active semester is set. Ask the admin to set Academic Year in Settings.',
            ], 422);
        }
        $result = $this->scheduler->assignMultiple($data);
        return response()->json($result, $result['success'] ? 200 : 422);
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
        return response()->json($this->scheduler->delete($id));
    }

    public function saveDraft(Request $request)
    {
        return response()->json(['success' => true, 'message' => 'Draft saved.']);
    }

    public function clear(Request $request)
    {
        $facultyId = $request->input('faculty');
        if (!$facultyId) {
            return response()->json(['success' => false, 'message' => 'No teacher selected.']);
        }

        $schedules = Schedule::where('sch_fac_id', $facultyId)
            ->when($request->filled('semester'), fn ($q) => $q->where('sch_sem_id', $request->input('semester')))
            ->get(['sch_id', 'sch_load_id', 'sch_fac_id', 'sch_sem_id']);

        Schedule::whereIn('sch_id', $schedules->pluck('sch_id'))->delete();
        Study_Load::whereIn('sl_id', $schedules->pluck('sch_load_id'))->delete();
        $schedules->unique(fn ($schedule) => $schedule->sch_fac_id.'|'.$schedule->sch_sem_id)
            ->each(fn ($schedule) => $this->scheduler->syncWorkload($schedule->sch_fac_id, $schedule->sch_sem_id));

        return response()->json(['success' => true, 'message' => 'Cleared.']);
    }
}
