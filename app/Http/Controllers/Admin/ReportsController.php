<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Semester;

class ReportsController extends Controller
{
    public function index()
    {
        $semester = Semester::where('sem_is_active', true)->first();

        $bySection = $this->getSectionData($semester);
        $byTeacher = $this->getTeacherData($semester);
        $byRoom    = $this->getRoomData($semester);

        return view('admin.reports', compact(
            'semester',
            'bySection',
            'byTeacher',
            'byRoom'
        ));
    }

    // ── BY SECTION ───────────────────────────────────────────────────────
    private function getSectionData($semester)
    {
        if (!$semester) return collect();

        return DB::table('section')
            ->join('program', 'section.sec_prog_id', '=', 'program.prog_id')
            ->leftJoin('schedule', 'schedule.sch_sec_id', '=', 'section.sec_id')
            ->leftJoin('study_load', 'schedule.sch_load_id', '=', 'study_load.sl_id')
            ->leftJoin('faculty', 'schedule.sch_fac_id', '=', 'faculty.fac_id')
            ->leftJoin(DB::raw('"USER"'), DB::raw('"USER".usr_id'), '=', 'faculty.fac_usr_id')
            ->leftJoin('subject', 'schedule.sch_subj_id', '=', 'subject.subj_id')
            ->leftJoin('room', 'schedule.sch_room_id', '=', 'room.room_id')
            ->where(function ($q) use ($semester) {
                $q->where('schedule.sch_sem_id', $semester->sem_id)
                  ->orWhereNull('schedule.sch_sem_id');
            })
            ->where('schedule.sch_is_active', true)
            ->select(
                'section.sec_id',
                'section.sec_name',
                'section.sec_year_level',
                'program.prog_code',
                DB::raw('"USER".usr_name as faculty_name'),
                'subject.subj_code',
                'subject.subj_name',
                'room.room_name',
                'schedule.sch_day        as day',
                'schedule.sch_start_time as start_time',
                'schedule.sch_end_time   as end_time'
            )
            ->orderBy('section.sec_name')
            ->orderBy('schedule.sch_day')
            ->orderBy('schedule.sch_start_time')
            ->get()
            ->groupBy('sec_name');
    }

    // ── BY TEACHER ───────────────────────────────────────────────────────
    private function getTeacherData($semester)
    {
        if (!$semester) return collect();

        return DB::table('faculty')
            ->join(DB::raw('"USER"'), DB::raw('"USER".usr_id'), '=', 'faculty.fac_usr_id')
            ->join('department', 'faculty.fac_dept_id', '=', 'department.dept_id')
            ->leftJoin('schedule', 'schedule.sch_fac_id', '=', 'faculty.fac_id')
            ->leftJoin('subject', 'schedule.sch_subj_id', '=', 'subject.subj_id')
            ->leftJoin('section', 'schedule.sch_sec_id', '=', 'section.sec_id')
            ->leftJoin('room', 'schedule.sch_room_id', '=', 'room.room_id')
            ->leftJoin('workload', function ($join) use ($semester) {
                $join->on('workload.wl_fac_id', '=', 'faculty.fac_id')
                     ->where('workload.wl_sem_id', '=', $semester->sem_id);
            })
            ->where(function ($q) use ($semester) {
                $q->where('schedule.sch_sem_id', $semester->sem_id)
                  ->orWhereNull('schedule.sch_sem_id');
            })
            ->where(function ($q) {
                $q->where('schedule.sch_is_active', true)
                  ->orWhereNull('schedule.sch_is_active');
            })
            ->select(
                'faculty.fac_id',
                DB::raw('"USER".usr_name as faculty_name'),
                'faculty.fac_employment_type',
                'department.dept_code',
                'subject.subj_code',
                'subject.subj_name',
                'section.sec_name',
                'room.room_name',
                'schedule.sch_day        as day',
                'schedule.sch_start_time as start_time',
                'schedule.sch_end_time   as end_time',
                DB::raw('COALESCE(workload.wl_total_hours, 0) as total_hours')
            )
            ->orderBy(DB::raw('"USER".usr_name'))
            ->orderBy('schedule.sch_day')
            ->orderBy('schedule.sch_start_time')
            ->get()
            ->groupBy('faculty_name');
    }

    // ── BY ROOM ──────────────────────────────────────────────────────────
    private function getRoomData($semester)
    {
        if (!$semester) return collect();

        return DB::table('room')
            ->leftJoin('schedule', function ($join) use ($semester) {
                $join->on('schedule.sch_room_id', '=', 'room.room_id')
                     ->where('schedule.sch_sem_id', '=', $semester->sem_id)
                     ->where('schedule.sch_is_active', '=', true);
            })
            ->leftJoin('faculty', 'schedule.sch_fac_id', '=', 'faculty.fac_id')
            ->leftJoin(DB::raw('"USER"'), DB::raw('"USER".usr_id'), '=', 'faculty.fac_usr_id')
            ->leftJoin('subject', 'schedule.sch_subj_id', '=', 'subject.subj_id')
            ->leftJoin('section', 'schedule.sch_sec_id', '=', 'section.sec_id')
            ->select(
                'room.room_id',
                'room.room_name',
                'room.room_type',
                'room.room_capacity',
                'room.room_is_available',
                DB::raw('"USER".usr_name as faculty_name'),
                'subject.subj_code',
                'subject.subj_name',
                'section.sec_name',
                'schedule.sch_day        as day',
                'schedule.sch_start_time as start_time',
                'schedule.sch_end_time   as end_time'
            )
            ->orderBy('room.room_name')
            ->orderBy('schedule.sch_day')
            ->orderBy('schedule.sch_start_time')
            ->get()
            ->groupBy('room_name');
    }
}