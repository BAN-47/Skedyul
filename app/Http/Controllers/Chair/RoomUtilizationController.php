<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Dept_Chair;
use App\Models\Course;
use App\Models\Departments;
use App\Models\Faculty;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class RoomUtilizationController extends Controller
{
    public function index(Request $request)
    {
        $chair = Dept_Chair::where('dc_usr_id', Auth::id())->firstOrFail();

        $academicYears = AcademicYear::with('semesters')->orderByDesc('ay_academic_year')->get();
        $currentYear = $academicYears->firstWhere('ay_is_active', true);
        $selectedYearId = (string) $request->query('academic_year', $currentYear?->ay_id ?? '');
        $selectedYear = $academicYears->firstWhere('ay_id', $selectedYearId) ?? $currentYear;
        $semesters = $selectedYear?->semesters?->sortBy('sem_start_date')->values() ?? collect();

        $currentSemester = Semester::where('sem_is_active', true)->first();
        $selectedSemesterId = (string) $request->query('semester', '');
        $selectedSemester = $semesters->firstWhere('sem_id', $selectedSemesterId);
        if (!$selectedSemester) {
            $selectedSemester = $currentSemester && (string) $currentSemester->sem_ay_id === (string) $selectedYear?->ay_id
                ? $semesters->firstWhere('sem_id', $currentSemester->sem_id)
                : $semesters->first();
        }

        $officialSemesterName = Semester::where('sem_is_active', true)->value('sem_name');
        $canPlotSelectedSemester = $selectedSemester && (
            ((bool) $selectedSemester->sem_is_active && (bool) $selectedYear?->ay_is_active)
            || ((bool) $selectedYear?->ay_is_active
                && $selectedSemester->sem_name === '2nd Semester'
                && $officialSemesterName === '1st Semester'
                && (bool) ($selectedSemester->sem_is_plotting_enabled ?? false))
        );

        // Chairs only see rooms belonging to their own college.
        $roomQuery = Room::query();
        if (Schema::hasColumn('room', 'room_college_id')) {
            $roomQuery->where('room_college_id', $chair->dc_college_id);
        } else {
            $roomQuery->where('room_building', 'CCICT Building');
        }
        $rooms = $roomQuery->orderBy('room_building')->orderBy('room_name')->get();

        // Room numbers such as NICT-804 map to floor 8; 1004 maps to floor 10.
        $rooms->each(function (Room $room): void {
            preg_match('/(\d{3,4})\s*$/', (string) $room->room_name, $matches);
            $digits = $matches[1] ?? '';
            $floor = $digits === '' ? null : (strlen($digits) === 4 ? (int) substr($digits, 0, 2) : (int) substr($digits, 0, 1));
            $room->setAttribute('utilization_floor', $floor >= 1 && $floor <= 10 ? $floor : null);
        });

        $floorFilter = (string) $request->query('floor', 'all');
        if ($floorFilter !== 'all' && !in_array($floorFilter, array_map('strval', range(1, 10)), true)) {
            $floorFilter = 'all';
        }
        $visibleRooms = $floorFilter === 'all'
            ? $rooms
            : $rooms->where('utilization_floor', (int) $floorFilter)->values();

        $selectedRoomId = (string) $request->query('room_id', '');
        $selectedRoom = $visibleRooms->firstWhere('room_id', $selectedRoomId) ?? $visibleRooms->first();
        $shift = $request->query('shift', 'day') === 'night' ? 'night' : 'day';

        $departments = Departments::where('dept_college_id', $chair->dc_college_id)->orderBy('dept_code')->get();
        $selectedDepartmentId = (string) $request->query('department', $chair->dc_dept_id ?? 'all');
        if ($selectedDepartmentId !== 'all' && !$departments->contains('dept_id', $selectedDepartmentId)) {
            $selectedDepartmentId = (string) ($chair->dc_dept_id ?? 'all');
        }
        $sections = $selectedSemester
            ? Section::query()->with('program')
                ->where('sec_sem_id', $selectedSemester->sem_id)
                ->whereIn('sec_dept_id', $selectedDepartmentId === 'all' ? $departments->pluck('dept_id') : [$selectedDepartmentId])
                ->orderBy('sec_name')->get()
            : collect();
        $courses = Course::query()->where('course_is_active', true)->orderBy('course_code')->get();
        $categories = Course::query()->whereNotNull('course_category')->where('course_category', '<>', '')
            ->distinct()->orderBy('course_category')->pluck('course_category');
        $faculty = Faculty::query()
            ->where(function ($query) { $query->whereHas('user')->orWhere('fac_is_placeholder', true); })
            ->with('user')->orderBy('fac_last_name')->orderBy('fac_first_name')->get();

        $schedules = collect();
        if ($selectedRoom && $selectedSemester) {
            $schedules = Schedule::query()
                ->where('sch_room_id', $selectedRoom->room_id)
                ->where('sch_sem_id', $selectedSemester->sem_id)
                ->where('sch_is_active', true)
                ->with(['subject', 'section', 'faculty.user'])
                ->orderBy('sch_start_time')
                ->get();

            // Keep the room grid consistent with the Day/Evening switch used by PBS and PBT.
            $schedules = $schedules->filter(function (Schedule $schedule) use ($shift): bool {
                $storedShift = strtolower((string) $schedule->sch_shift);
                return $shift === 'night'
                    ? (str_contains($storedShift, 'even') || str_contains($storedShift, 'night'))
                    : (str_contains($storedShift, 'day') || $storedShift === '');
            })->values();
        }

        $schedulesByDay = $schedules->groupBy('sch_day');
        $scheduledHours = $schedules->sum(function (Schedule $schedule): float {
            $start = \Illuminate\Support\Carbon::parse($schedule->sch_start_time);
            $end = \Illuminate\Support\Carbon::parse($schedule->sch_end_time);
            return $start->diffInMinutes($end) / 60;
        });

        return view('chair.room_utilization', compact(
            'academicYears', 'selectedYear', 'selectedYearId', 'semesters', 'selectedSemester',
            'floorFilter', 'visibleRooms', 'selectedRoom', 'schedulesByDay', 'scheduledHours', 'shift',
            'sections', 'courses', 'categories', 'faculty', 'canPlotSelectedSemester',
            'departments', 'selectedDepartmentId'
        ));
    }
}
