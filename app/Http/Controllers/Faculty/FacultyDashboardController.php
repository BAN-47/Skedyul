<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Study_Load;
use App\Models\Semester;
use Carbon\Carbon;

class FacultyDashboardController extends Controller
{
    public function index()
    {
        $faculty = Faculty::where('fac_usr_id', auth()->id())->firstOrFail();

        $facultyBios = Faculty::with('department')
            ->where('fac_id', '!=', $faculty->fac_id)
            ->whereNotNull('fac_bio')
            ->whereRaw("TRIM(fac_bio) <> ''")
            ->orderBy('fac_last_name')
            ->orderBy('fac_first_name')
            ->get();

        $activeSemester = Semester::where('sem_is_active', true)->first();

        $facultyLoads = Study_Load::where('sl_fac_id', $faculty->fac_id)
            ->when($activeSemester, fn ($q) => $q->where('sl_sem_id', $activeSemester->sem_id))
            ->with('subject')
            ->get();
        if ($faculty->fac_employment_type === 'part_time') {
            $dayHours = (float) $facultyLoads->sum(fn ($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject));
            $eveningHours = 0;
            $dayMaxHours = 30;
            $eveningMaxHours = 0;
            $totalHours = $dayHours;
            $maxHours = 30;
        } else {
            $dayHours = (float) $facultyLoads->where('sl_shift', 'day')
                ->sum(fn ($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject));
            $eveningHours = (float) $facultyLoads->where('sl_shift', 'evening')
                ->sum(fn ($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject));
            $dayMaxHours = \App\Services\ScheduleAssignmentService::facultyMaxHours($faculty, 'day');
            $eveningMaxHours = \App\Services\ScheduleAssignmentService::facultyMaxHours($faculty, 'evening');
            $totalHours = $dayHours + $eveningHours;
            $maxHours = $dayMaxHours + $eveningMaxHours;
        }

        $schedules = Schedule::with(['subject', 'section', 'room'])
            ->where('sch_fac_id', $faculty->fac_id)
            ->where('sch_is_active', true)
            ->when($activeSemester, fn ($q) => $q->where('sch_sem_id', $activeSemester->sem_id))
            ->get();

        $mySubjects = $schedules->unique('sch_subj_id')->map(fn ($s) => $s->subject)->filter()->values();
        $mySections = $schedules->pluck('section.sec_name')->filter()->unique()->values();

        $today = Carbon::now()->format('l');
        $todaySchedule = $schedules->where('sch_day', $today)->sortBy('sch_start_time')->values();
        $weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $weeklyScheduleCounts = collect($weekDays)->map(fn ($day) => [
            'day' => substr($day, 0, 3),
            'count' => $schedules->where('sch_day', $day)->count(),
        ]);

        return view('faculty.faculty_dashboard', compact(
            'faculty', 'facultyBios', 'totalHours', 'maxHours', 'dayHours', 'dayMaxHours',
            'eveningHours', 'eveningMaxHours', 'weeklyScheduleCounts',
            'mySubjects', 'mySections', 'todaySchedule', 'today'
        ));
    }
}
