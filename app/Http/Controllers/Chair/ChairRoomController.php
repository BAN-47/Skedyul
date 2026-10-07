<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Faculty;
use App\Models\Course;
use App\Models\Section;
use App\Models\Study_Load;
use App\Services\ScheduleAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

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

        $roomQuery = Room::query();
        if (Schema::hasColumn('room', 'room_college_id')) {
            $roomQuery->where('room_college_id', $deptChair->dc_college_id);
        } else {
            // Continue showing the legacy campus rooms until the Supabase column is added.
            $roomQuery->where('room_building', 'CCICT Building');
        }
        $rooms = $roomQuery->orderBy('room_name')->get();
        $building = $rooms->pluck('room_building')->filter()->unique()->implode(', ') ?: 'Rooms assigned to your college';

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
        $subjects = Course::where('course_college_id', $deptChair->dc_college_id)
            ->when($deptChair->dc_prog_id, fn($q) => $q->where('course_dept_id', $deptChair->dc_prog_id))
            ->where('course_is_active', true)
            ->orderBy('course_code')
            ->get();

        $subjectsById = $subjects->keyBy('subj_id');

        $facultyRecords = Faculty::where('fac_college_id', $deptChair->dc_college_id)
            ->orderBy('fac_first_name')
            ->get();

        $facultyIds = $facultyRecords->pluck('fac_id');
        $studyLoads = Study_Load::whereIn('sl_fac_id', $facultyIds)->get()->groupBy('sl_fac_id');

        $faculty = $facultyRecords->map(function (Faculty $f) use ($studyLoads, $subjectsById) {
            $totalUnits = $studyLoads->get($f->fac_id, collect())->sum(function ($sl) use ($subjectsById) {
                $s = $subjectsById->get($sl->sl_subj_id);
                return $s ? (float) ($s->subj_units ?? ((float) $s->subj_lecture_hours + (float) $s->subj_lab_hours)) : 0;
            });
            return [
                'id'    => $f->fac_id,
                'name'  => trim("{$f->fac_first_name} {$f->fac_last_name}"),
                'units' => $totalUnits,
            ];
        });

        $sections = Section::when($deptChair->dc_prog_id, fn($q) => $q->where('sec_dept_id', $deptChair->dc_prog_id))
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
        $chair = Dept_Chair::where('dc_usr_id', Auth::id())->first();
        abort_if(!$chair, 403, 'Your account is not assigned as a department chair.');

        $validated = $request->validate([
            'room_name' => 'required|string|max:100',
            'room_type' => 'required|string|max:50',
            'room_capacity' => 'required|integer|min:1',
            'room_building' => 'required|string|max:100',
            'room_location' => 'nullable|string|max:150',
        ]);

        if (!Schema::hasColumn('room', 'room_college_id')) {
            return back()->withInput()->with('error', 'Add the room_college_id column in Supabase before creating college-assigned rooms.');
        }
        // The college comes from the chair's account, never from user input.
        $validated['room_college_id'] = $chair->dc_college_id;
        $validated['room_is_available'] = true;

        try {
            $duplicate = Room::query()
                ->whereRaw('LOWER(TRIM(room_name)) = ?', [mb_strtolower(trim($validated['room_name']))])
                ->where('room_college_id', $chair->dc_college_id)
                ->exists();
            if ($duplicate) {
                return back()->withInput()->withErrors(['room_name' => 'A room with this name already exists for your college.']);
            }

            Room::create($validated);
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Unable to add this room. Check that the room_college_id column exists in Supabase, then try again.');
        }

        return redirect()->route('chair.rooms')->with('success', 'Room added and assigned to your college.');
    }

    public function assignSchedule(Request $request)
    {
        $validated = $request->validate([
            'subj_id'        => 'required|uuid|exists:course,course_id',
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
            'subj_id'        => 'required|uuid|exists:course,course_id',
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
