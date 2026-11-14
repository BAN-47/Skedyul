<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Dean;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Study_Load;
use App\Models\SystemSetting;
use App\Services\ScheduleSignatoryFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacultyScheduleController extends Controller
{
    public function index(Request $request)
    {
        $faculty = Faculty::where('fac_usr_id', auth()->id())->firstOrFail();
        $activeSemester = $request->filled('semester')
            ? Semester::with('academicYear')->find($request->query('semester'))
            : null;
        $activeSemester ??= Semester::with('academicYear')
            ->where('sem_is_active', true)
            ->first();
        $activeSemester ??= Semester::with('academicYear')
            ->orderByDesc('sem_start_date')
            ->first();

        if ($activeSemester) {
            $year = $activeSemester->academicYear?->ay_academic_year
                ?? $activeSemester->academicYear?->ay_year_label
                ?? '';
            $yearLabel = $year !== '' ? str_replace('-', ' - ', $year) : '';
            $activeSemester->label = trim(
                ($activeSemester->sem_name ?? '')
                . ($yearLabel !== '' ? ', AY ' . $yearLabel : '')
            );
        }

        $semesters = Semester::with('academicYear')
            ->orderByDesc('sem_start_date')
            ->get()
            ->filter(fn($sem) => $sem->sem_is_active || $sem->sem_is_plotting_enabled)
            ->map(function (Semester $semester) {
                $year = $semester->academicYear?->ay_academic_year
                    ?? $semester->academicYear?->ay_year_label
                    ?? '';
                $yearLabel = $year !== '' ? str_replace('-', ' - ', $year) : '';
                $semester->label = trim(
                    ($semester->sem_name ?? '')
                    . ($yearLabel !== '' ? ', AY ' . $yearLabel : '')
                    . ($semester->sem_is_active ? ' (Current)' : '')
                );

                return $semester;
            });

        $shiftKey = $request->query('shift', 'day') === 'night' ? 'evening' : 'day';

        $allSchedules = Schedule::with(['subject', 'section', 'room'])
            ->where('sch_fac_id', $faculty->fac_id)
            ->where('sch_is_active', true)
            ->when(
                $activeSemester,
                fn ($query) => $query->where('sch_sem_id', $activeSemester->sem_id),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->when(
                $shiftKey === 'evening',
                fn ($q) => $q->whereRaw("CAST(sch_start_time AS TIME) < '22:00:00' AND CAST(sch_end_time AS TIME) > '16:00:00'"),
                fn ($q) => $q->whereRaw("CAST(sch_start_time AS TIME) < '16:00:00' AND CAST(sch_end_time AS TIME) > '07:00:00'")
            )
            ->orderBy('sch_start_time')
            ->get();

        $schedules = $allSchedules;

        $summarySchedules = Schedule::query()
            ->where('sch_fac_id', $faculty->fac_id)
            ->when(
                $activeSemester,
                fn ($q) => $q->where('sch_sem_id', $activeSemester->sem_id),
                fn ($q) => $q->whereRaw('1 = 0')
            )
            ->where('sch_is_active', true)
            ->whereHas('studyLoad', fn ($q) => $q->where('sl_fac_id', $faculty->fac_id))
            ->where(function ($query) use ($shiftKey) {
                if ($shiftKey === 'evening') {
                    $query->whereHas('studyLoad', fn ($load) => $load->where('sl_shift', 'evening'))
                        ->orWhereHas('section', fn ($section) => $section->where('sec_is_evening', true));
                } else {
                    $query->whereHas('studyLoad', fn ($load) => $load->where('sl_shift', 'day'))
                        ->whereDoesntHave('section', fn ($section) => $section->where('sec_is_evening', true));
                }
            })
            ->with(['subject', 'section', 'room', 'studyLoad'])
            ->orderBy('sch_day')
            ->orderBy('sch_start_time')
            ->get();

        $studyLoads = Study_Load::where('sl_fac_id', $faculty->fac_id)
            ->when(
                $activeSemester,
                fn ($q) => $q->where('sl_sem_id', $activeSemester->sem_id),
                fn ($q) => $q->whereRaw('1 = 0')
            )
            ->with('subject', 'section', 'schedules')
            ->get();

        $shiftForLoad = fn ($load) => $load->section?->sec_is_evening
            ? 'evening'
            : ($load->sl_shift ?? 'day');
        $loads = $studyLoads->filter(fn ($load) => $shiftForLoad($load) === $shiftKey)->values();

        $units = $loads->sum(fn ($load) => (float) ($load->subject?->course_units ?? 0));
        $hoursForLoads = function ($loads) {
            return $loads->sum(function ($load) {
                return $load->schedules->where('sch_is_active', true)->sum(function ($schedule) {
                    $start = \Illuminate\Support\Carbon::parse($schedule->sch_start_time);
                    $end = \Illuminate\Support\Carbon::parse($schedule->sch_end_time);
                    return $start->diffInMinutes($end) / 60;
                });
            });
        };
        $hoursPerWeek = $hoursForLoads($loads);
        $totalHoursPerWeek = $faculty->fac_employment_type === 'part_time'
            ? $hoursForLoads($studyLoads)
            : null;
        $workloadHours = $loads->sum(fn ($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject));
        $totalWorkloadHours = $faculty->fac_employment_type === 'part_time'
            ? $studyLoads->sum(fn ($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject))
            : null;

        $courseRows = $summarySchedules
            ->filter(fn ($schedule) => $schedule->subject && ($schedule->subject->course_code ?? '') !== 'REC')
            ->unique(fn ($schedule) => $schedule->sch_course_id . '|' . $schedule->sch_sec_id)
            ->values();

        $loadStats = [
            'preparations' => $loads->filter(fn($l) => ($l->subject->course_code ?? '') !== 'REC')->pluck('sl_course_id')->unique()->count(),
            'units' => $units ?: null,
            'hours_week' => $hoursPerWeek ?: null,
            'total_hours_week' => $totalHoursPerWeek ?: null,
            'workload_hours' => $workloadHours ?: null,
            'total_workload_hours' => $totalWorkloadHours ?: null,
            'is_part_time' => $faculty->fac_employment_type === 'part_time',
            'designation' => $faculty->fac_special_position ?: $faculty->fac_rank,
            'load_limit' => \App\Services\ScheduleAssignmentService::facultyMaxHours($faculty, $shiftKey),
            'production' => null,
            'extension' => null,
            'research' => null,
        ];

        $shift = $shiftKey === 'evening' ? 'night' : 'day';

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        $department = $faculty->program;
        $chair = $department
            ? Dept_Chair::where('dc_dept_id', $department->dept_id)->first()
            : null;
        $dean = $faculty->fac_college_id
            ? Dean::where('dean_college_id', $faculty->fac_college_id)->first()
            : null;

        $signatoryFormatter = app(ScheduleSignatoryFormatter::class);

        $signatories = [
            'chair_name' => $signatoryFormatter->formatRoleName($chair, 'dc_'),
            'chair_title' => 'Chair' . ($department?->dept_code ? ', ' . $department->dept_code : ''),
            'dean_name' => $signatoryFormatter->formatRoleName($dean, 'dean_'),
            'dean_title' => 'Dean' . ($faculty->college?->college_code ? ', ' . $faculty->college->college_code : ''),
            'campus_director' => SystemSetting::get('campus_director_name', ''),
        ];

        return view('faculty.schedule', compact(
            'faculty',
            'allSchedules',
            'schedules',
            'summarySchedules',
            'courseRows',
            'loadStats',
            'activeSemester',
            'semesters',
            'days',
            'shift',
            'signatories'
        ));
    }
}
