<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\room as Room;
use App\Models\Schedule;
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

        return view('chair.rooms', compact(
            'academicYear', 'semester', 'building',
            'roomData', 'totalRooms', 'availableCount', 'inUseCount', 'laboratories'
        ));
    }
}