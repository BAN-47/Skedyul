<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Semester;

class FacultySubjectsController extends Controller
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

        $subjects = $schedules->groupBy('sch_subj_id')->map(function ($group) {
            $first = $group->first();
            return [
                'subject'   => $first->subject,
                'sections'  => $group->pluck('section.sec_name')->filter()->unique()->implode(', '),
                'room'      => $first->room->room_name ?? 'N/A',
                'schedules' => $group->map(fn ($s) => [
                    'day'   => $s->sch_day,
                    'start' => $s->sch_start_time,
                    'end'   => $s->sch_end_time,
                ]),
            ];
        })->values();

        return view('faculty.subjects', compact('faculty', 'subjects', 'activeSemester'));
    }
}