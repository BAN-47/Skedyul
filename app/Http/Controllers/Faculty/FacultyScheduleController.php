<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Semester;
use Carbon\Carbon;

class FacultyScheduleController extends Controller
{
    public function index()
    {
        $faculty = Faculty::where('fac_usr_id', auth()->id())->firstOrFail();
        $activeSemester = Semester::where('sem_is_active', true)->first();

        $schedules = Schedule::with(['subject', 'section', 'room'])
            ->where('sch_fac_id', $faculty->fac_id)
            ->where('sch_is_active', true)
            ->when($activeSemester, fn ($q) => $q->where('sch_sem_id', $activeSemester->sem_id))
            ->get();

        $weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $byDay = collect($weekDays)->mapWithKeys(fn ($day) => [
            $day => $schedules->where('sch_day', $day)->sortBy('sch_start_time')->values()
        ]);

        $now = Carbon::now();
        $today = $now->format('l');
        $currentSchedule = $schedules->first(function ($sch) use ($today, $now) {
            if ($sch->sch_day !== $today) return false;
            $start = Carbon::createFromTimeString($sch->sch_start_time);
            $end = Carbon::createFromTimeString($sch->sch_end_time);
            $nowTime = Carbon::createFromTimeString($now->format('H:i:s'));
            return $nowTime->between($start, $end);
        });

        $nextInRoom = null;
        if ($currentSchedule) {
            $nextInRoom = $schedules
                ->where('sch_day', $today)
                ->where('sch_room_id', $currentSchedule->sch_room_id)
                ->where('sch_start_time', '>', $currentSchedule->sch_end_time)
                ->sortBy('sch_start_time')
                ->first();
        }

        return view('faculty.schedule', compact(
            'faculty', 'schedules', 'byDay', 'weekDays', 'currentSchedule', 'nextInRoom', 'activeSemester'
        ));
    }
}
?>