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

        $totalHours = Study_Load::where('sl_fac_id', $faculty->fac_id)
            ->when($activeSemester, fn ($q) => $q->where('sl_sem_id', $activeSemester->sem_id))
            ->with('subject')
            ->get()
            ->sum(fn ($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject));
        $maxHours = \App\Services\ScheduleAssignmentService::facultyMaxHours($faculty);

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
            'faculty', 'facultyBios', 'totalHours', 'maxHours', 'mySubjects', 'mySections', 'todaySchedule', 'today'
        ));
    }
}
