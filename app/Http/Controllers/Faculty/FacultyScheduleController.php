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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacultyScheduleController extends Controller
{
    public function index(Request $request)
    {
        $faculty = Faculty::where('fac_usr_id', auth()->id())->firstOrFail();
        $activeSemester = Semester::where('sem_is_active', true)->first();
        $activeYear = DB::table('academic_year')->where('ay_is_active', true)->first();

        if ($activeSemester) {
            $year = $activeYear->ay_academic_year ?? $activeYear->ay_year_label ?? '';
            $yearLabel = $year !== '' ? str_replace('-', ' - ', $year) : '';
            $activeSemester->label = trim(
                ($activeSemester->sem_name ?? '')
                . ($yearLabel !== '' ? ', AY ' . $yearLabel : '')
            );
        }

        $shift = $request->query('shift') === 'night' ? 'night' : 'day';
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        $allSchedules = Schedule::with(['subject', 'section', 'room'])
            ->where('sch_fac_id', $faculty->fac_id)
            ->where('sch_is_active', true)
            ->when(
                $activeSemester,
                fn ($query) => $query->where('sch_sem_id', $activeSemester->sem_id),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->orderBy('sch_start_time')
            ->get();

        $schedules = $allSchedules->filter(function ($schedule) use ($shift) {
            $hour = (int) substr((string) $schedule->sch_start_time, 0, 2);
            return $shift === 'night' ? $hour >= 16 : $hour < 16;
        })->values();

        $courseRows = $allSchedules
            ->filter(fn ($schedule) => $schedule->subject)
            ->unique(fn ($schedule) => $schedule->sch_course_id . '|' . $schedule->sch_sec_id)
            ->values();

        // Match PBT: units/preparations come from assigned Study Loads,
        // while weekly hours come from the meetings attached to those loads.
        $studyLoads = Study_Load::where('sl_fac_id', $faculty->fac_id)
            ->when($activeSemester, fn ($query) => $query->where('sl_sem_id', $activeSemester->sem_id))
            ->with(['subject', 'schedules'])
            ->get();

        $units = $studyLoads->sum(fn ($load) =>
            $load->subject?->course_units !== null
                ? (float) $load->subject->course_units
                : (float) ($load->subject->course_lecture_hours ?? 0)
                    + (float) ($load->subject->course_lab_hours ?? 0)
        );
        $hoursPerWeek = $studyLoads->sum(fn ($load) => $load->schedules->sum(function ($schedule) {
            $start = \Illuminate\Support\Carbon::parse($schedule->sch_start_time);
            $end = \Illuminate\Support\Carbon::parse($schedule->sch_end_time);
            return $start->diffInMinutes($end) / 60;
        }));

        $loadStats = [
            'preparations' => $studyLoads->pluck('sl_course_id')->unique()->count(),
            'units' => $units ?: null,
            'hours_week' => $hoursPerWeek ?: null,
            'designation' => $faculty->fac_rank,
            'production' => null,
            'extension' => null,
            'research' => null,
        ];

        $department = $faculty->program;
        $chair = $department
            ? Dept_Chair::where('dc_dept_id', $department->dept_id)->first()
            : null;
        $dean = $faculty->fac_college_id
            ? Dean::where('dean_college_id', $faculty->fac_college_id)->first()
            : null;

        $personName = static function ($person, string $prefix): string {
            if (!$person) return '';

            $first = trim((string) ($person->{$prefix . 'first_name'} ?? ''));
            $middle = trim((string) ($person->{$prefix . 'middle_name'} ?? ''));
            $last = trim((string) ($person->{$prefix . 'last_name'} ?? ''));
            $suffix = trim((string) ($person->{$prefix . 'suffix'} ?? ''));
            $parts = array_filter([
                $first,
                $middle !== '' ? mb_strtoupper(mb_substr($middle, 0, 1)) . '.' : '',
                $last,
            ]);
            $name = implode(' ', $parts);

            return $suffix !== '' ? $name . ', ' . $suffix : $name;
        };

        $signatories = [
            'chair_name' => $personName($chair, 'dc_'),
            'chair_title' => 'Chair' . ($department?->dept_code ? ', ' . $department->dept_code : ''),
            'dean_name' => $personName($dean, 'dean_'),
            'dean_title' => 'Dean' . ($faculty->college?->college_code ? ', ' . $faculty->college->college_code : ''),
            'campus_director' => SystemSetting::get('campus_director_name', ''),
        ];

        return view('faculty.schedule', compact(
            'faculty',
            'allSchedules',
            'schedules',
            'courseRows',
            'loadStats',
            'activeSemester',
            'days',
            'shift',
            'signatories'
        ));
    }
}
