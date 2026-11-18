<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Study_Load;

class FacultySubjectsController extends Controller
{
    public function index()
    {
        $faculty = Faculty::where('fac_usr_id', auth()->id())->firstOrFail();

        $activeSemester = Semester::with('academicYear')->where('sem_is_active', true)->first();

        $schedules = Schedule::with(['subject', 'section', 'room'])
            ->where('sch_fac_id', $faculty->fac_id)
            ->where('sch_is_active', true)
            ->when($activeSemester, fn ($q) => $q->where('sch_sem_id', $activeSemester->sem_id))
            ->get();

        $subjects = $schedules->groupBy('sch_subj_id')->map(function ($group) {
            $first = $group->first();
            $sections = $group->pluck('section')->filter()->unique('sec_id')->values();
            $rooms = $group->pluck('room.room_name')->filter()->unique()->values();
            return [
                'subject'   => $first->subject,
                'sections'  => $sections->pluck('sec_name')->implode(', '),
                'section_count' => $sections->count(),
                'students' => (int) $sections->sum(fn ($section) => $section->sec_no_of_student ?? 0),
                'room'      => $rooms->isNotEmpty() ? $rooms->implode(', ') : 'N/A',
                'rooms'     => $rooms,
                'schedules' => $group->map(fn ($s) => [
                    'day'   => $s->sch_day,
                    'start' => $s->sch_start_time,
                    'end'   => $s->sch_end_time,
                    'room'  => $s->room->room_name ?? 'N/A',
                ]),
            ];
        })->values();

        $studyLoads = Study_Load::where('sl_fac_id', $faculty->fac_id)
            ->when($activeSemester, fn ($query) => $query->where('sl_sem_id', $activeSemester->sem_id))
            ->with('subject')
            ->get();
        $weeklyContactLoad = (float) $studyLoads->sum(
            fn ($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject)
        );
        $uniqueSections = $schedules->pluck('section')->filter()->unique('sec_id');
        $sectionCount = $uniqueSections->count();
        $enrolledStudents = (int) $uniqueSections->sum(fn ($section) => $section->sec_no_of_student ?? 0);
        $maxHours = $faculty->fac_employment_type === 'part_time'
            ? 30
            : \App\Services\ScheduleAssignmentService::facultyMaxHours($faculty, 'day')
                + \App\Services\ScheduleAssignmentService::facultyMaxHours($faculty, 'evening');
        $labSubjectCount = $subjects->filter(fn ($entry) => (float) ($entry['subject']?->subj_lab_hours ?? 0) > 0)->count();
        $labRoomNames = $subjects
            ->filter(fn ($entry) => (float) ($entry['subject']?->subj_lab_hours ?? 0) > 0)
            ->flatMap(fn ($entry) => $entry['rooms'])
            ->unique()
            ->values();

        return view('faculty.subjects', compact(
            'faculty', 'subjects', 'activeSemester', 'weeklyContactLoad',
            'enrolledStudents', 'sectionCount', 'maxHours', 'labSubjectCount', 'labRoomNames'
        ));
    }
}
