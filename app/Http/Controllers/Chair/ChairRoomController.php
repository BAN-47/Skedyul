<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\room as Room;
use App\Models\Schedule;
use App\Models\Faculty;
use App\Models\Subjects;
use App\Models\Section;
use App\Models\Study_Load;
use App\Services\ScheduleAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChairRoomController extends Controller
{
    public function index()
    {
        $deptChair = Dept_Chair::where('dc_usr_id', Auth::id())->first();
        if (!$deptChair) {
            abort(403, 'Your account is not assigned as a department chair.');
        }

        $academicYear = AcademicYear::where('ay_is_active', true)->first();
        $semester     = Semester::where('sem_is_active', true)->first();

        $building = 'CCICT Building';

        $rooms = Room::where('room_building', $building)
            ->orderBy('room_name')
            ->get();

        $schedules = $semester
            ? Schedule::with(['subject', 'section'])
                ->where('sch_sem_id', $semester->sem_id)
                ->where('sch_is_active', true)
                ->whereIn('sch_room_id', $rooms->pluck('room_id'))
                ->get()
                ->groupBy('sch_room_id')
            : collect();

        $roomData = $rooms->map(fn ($room) => [
            'room'      => $room,
            'schedules' => $schedules->get($room->room_id, collect()),
            'is_booked' => $schedules->get($room->room_id, collect())->isNotEmpty(),
        ]);

        $totalRooms     = $rooms->count();
        $laboratories   = $rooms->where('room_type', 'Laboratory')->count();
        $inUseCount     = $roomData->where('is_booked', true)->count();
        $availableCount = $totalRooms - $inUseCount;

        // ---------- Data the Assign / Edit Schedule modals need ----------
        $subjects = Subjects::where('subj_dept_id', $deptChair->dc_dept_id)
            ->when($deptChair->dc_prog_id, fn($q) => $q->where('subj_prog_id', $deptChair->dc_prog_id))
            ->where('subj_is_active', true)
            ->orderBy('subj_code')
            ->get();

        $subjectsById = $subjects->keyBy('subj_id');

        $facultyRecords = Faculty::where('fac_dept_id', $deptChair->dc_dept_id)
            ->orderBy('fac_first_name')
            ->get();

        $facultyIds = $facultyRecords->pluck('fac_id');
        $studyLoads = Study_Load::whereIn('sl_fac_id', $facultyIds)->get()->groupBy('sl_fac_id');

        $faculty = $facultyRecords->map(function (Faculty $f) use ($studyLoads, $subjectsById) {
            $totalUnits = $studyLoads->get($f->fac_id, collect())->sum(function ($sl) use ($subjectsById) {
                $s = $subjectsById->get($sl->sl_subj_id);
                return $s ? ((float) $s->subj_lecture_hours + (float) $s->subj_lab_hours) : 0;
            });
            return [
                'id'    => $f->fac_id,
                'name'  => trim("{$f->fac_first_name} {$f->fac_last_name}"),
                'units' => $totalUnits,
            ];
        });

        $sections = Section::when($deptChair->dc_prog_id, fn($q) => $q->where('sec_prog_id', $deptChair->dc_prog_id))
            ->when($academicYear, fn($q) => $q->where('sec_ay_id', $academicYear->ay_id))
            ->when($semester, fn($q) => $q->where('sec_sem_id', $semester->sem_id))
            ->orderBy('sec_name')
            ->get();

        return view('chair.rooms', compact(
            'academicYear', 'semester', 'building',
            'roomData', 'totalRooms', 'availableCount', 'inUseCount', 'laboratories',
            'subjects', 'faculty', 'sections'
        ));
    }

    public function store(Request $request)
    {
        // ASSUMPTION: adding brand-new physical rooms belongs to the Technical
        // Admin's Room Management page, not the Chair. This route was already
        // wired in your routes file, so it's kept functional (no fatal error
        // if it's ever hit) rather than left calling an undefined method —
        // but it intentionally doesn't create anything yet. Tell me if Chairs
        // should actually be able to add rooms and I'll build it properly.
        return back()->with('info', 'Adding new rooms is managed by the Technical Admin, not from this page.');
    }

    public function assignSchedule(Request $request)
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

    public function updateSchedule(Request $request, string $schedule)
    {
        $scheduleModel = Schedule::findOrFail($schedule);

        $validated = $request->validate([
            'subj_id'        => 'required|uuid|exists:subject,subj_id',
            'fac_id'         => 'required|uuid|exists:faculty,fac_id',
            'sec_id'         => 'required|uuid|exists:section,sec_id',
            'room_id'        => 'required|uuid|exists:room,room_id',
            'sch_day'        => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'sch_start_time' => 'required',
            'sch_end_time'   => 'required',
        ]);

        $result = app(ScheduleAssignmentService::class)->update($scheduleModel, $validated);
        $status = $result['status'];
        unset($result['status']);

        return response()->json($result, $status);
    }
}