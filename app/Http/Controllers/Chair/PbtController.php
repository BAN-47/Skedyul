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
use Illuminate\Support\Facades\DB;

class PbtController extends Controller
{
    public function __construct(private ScheduleAssignmentService $scheduler)
    {
    }

    public function index(Request $request)
    {
        $filters   = $request->only(['program', 'semester']);
        $facultyId = $request->query('faculty');

        $selectedFaculty = $facultyId ? Faculty::find($facultyId) : null;

        $schedules = collect();
        if ($selectedFaculty) {
            $schedules = Schedule::query()
                ->where('sch_fac_id', $selectedFaculty->fac_id)
                ->when(!empty($filters['semester']), fn ($q) => $q->where('sch_sem_id', $filters['semester']))
                ->where('sch_is_active', true)
                ->with(['subject', 'section', 'room'])
                ->get();
        }

        return view('chair.pbt', [
            'schedules'        => $schedules,
            'subjects'         => Course::where('course_is_active', true)->orderBy('course_code')->get(),
            'facultyFullTime'  => Faculty::where('fac_employment_type', 'full_time')->orderBy('fac_last_name')->get(),
            'facultyPartTime'  => Faculty::where('fac_employment_type', 'part_time')->orderBy('fac_last_name')->get(),
            // schedule.sch_fac_id has a foreign key to faculty(fac_id) only —
            // a Dean can be picked here ONLY if they also have their own row
            // in `faculty` (the two tables share usr_id when one person holds
            // both roles). Deans with no matching faculty row simply won't
            // appear, since there'd be no valid fac_id to schedule against.
            'deans'            => Faculty::whereIn('fac_usr_id', Dean::pluck('dean_usr_id'))->orderBy('fac_last_name')->get(),
            'sections'         => DB::table('section')->orderBy('sec_name')->get(),
            'programs'         => Departments::orderBy('dept_name')->get(),
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
                    $label = trim($ay . ($ay !== '' ? ' · ' : '') . $sem->sem_name);
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
            ->with('subject', 'schedule')
            ->get();

        $units = $loads->sum(fn ($l) => ($l->subject->subj_lecture_hours ?? 0) + ($l->subject->subj_lab_hours ?? 0));

        $hoursPerWeek = $loads->sum(function ($l) {
            if (!$l->schedule) return 0;
            $start = \Illuminate\Support\Carbon::parse($l->schedule->sch_start_time);
            $end   = \Illuminate\Support\Carbon::parse($l->schedule->sch_end_time);
            return $end->diffInMinutes($start) / 60;
        });

        return [
            'preparations' => $loads->pluck('sl_subj_id')->unique()->count(),
            'units'        => $units ?: null,
            'hours_week'   => $hoursPerWeek ?: null,
            'designation'  => $faculty->fac_rank,
            'production'   => null,
            'extension'    => null,
            'research'     => null,
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'subj_id'    => 'required|uuid|exists:course,course_id',
            'fac_id'     => 'required|uuid|exists:faculty,fac_id',
            'sem_id'     => 'required|uuid|exists:semester,sem_id',
            'sec_id'     => 'required|uuid|exists:section,sec_id',
            'room_id'    => 'required|uuid|exists:room,room_id',
            'day'        => 'required|string|max:15',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
        ]);
    }

    public function store(Request $request)
    {
        $result = $this->scheduler->assign($this->validated($request));
        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function update(Request $request, string $id)
    {
        $result = $this->scheduler->update($id, $this->validated($request));
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

        $ids = Schedule::where('sch_fac_id', $facultyId)
            ->when($request->filled('semester'), fn ($q) => $q->where('sch_sem_id', $request->input('semester')))
            ->pluck('sch_load_id', 'sch_id');

        Schedule::whereIn('sch_id', $ids->keys())->delete();
        Study_Load::whereIn('sl_id', $ids->values())->delete();

        return response()->json(['success' => true, 'message' => 'Cleared.']);
    }
}