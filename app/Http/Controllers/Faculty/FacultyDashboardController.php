<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Workload;
use App\Models\Semester;
use Carbon\Carbon;

class FacultyDashboardController extends Controller
{
    public function index()
    {
        $faculty = Faculty::where('fac_usr_id', auth()->id())->firstOrFail();

        $activeSemester = Semester::where('sem_is_active', true)->first();

        $totalHours = Workload::where('wl_fac_id', $faculty->fac_id)
            ->when($activeSemester, fn ($q) => $q->where('wl_sem_id', $activeSemester->sem_id))
            ->sum('wl_total_hours');

        $schedules = Schedule::with(['subject', 'section', 'room'])
            ->where('sch_fac_id', $faculty->fac_id)
            ->where('sch_is_active', true)
            ->when($activeSemester, fn ($q) => $q->where('sch_sem_id', $activeSemester->sem_id))
            ->get();

        $mySubjects = $schedules->unique('sch_subj_id')->map(fn ($s) => $s->subject)->filter()->values();
        $mySections = $schedules->pluck('section.sec_name')->filter()->unique()->values();

        $today = Carbon::now()->format('l');
        $todaySchedule = $schedules->where('sch_day', $today)->sortBy('sch_start_time')->values();

        return view('faculty.faculty_dashboard', compact(
            'faculty', 'totalHours', 'mySubjects', 'mySections', 'todaySchedule', 'today'
        ));
    }
}