<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Section;
use App\Models\Subjects;
use App\Models\Room;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Study_Load;
use App\Models\Schedule;
use App\Models\Workload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    protected function currentDept()
    {
        $deptChair = Dept_Chair::where('dc_usr_id', Auth::id())->first();
        abort_if(!$deptChair, 403, 'Your account is not assigned as a department chair.');
        return $deptChair->dc_dept_id;
    }

    // Time-slot generator — 7:00 AM to 9:00 PM, hourly.
    // Same shape the view expects: 'start' / 'end' (H:i) and a display 'label'.
    protected function hourlySlots(): array
    {
        $slots = [];
        for ($h = 7; $h < 21; $h++) { // 7 → 20, giving 7–8am ... 8–9pm
            $start = sprintf('%02d:00', $h);
            $end   = sprintf('%02d:00', $h + 1);
            $slots[] = [
                'label' => date('g:i A', strtotime($start)) . ' – ' . date('g:i A', strtotime($end)),
                'start' => $start,
                'end'   => $end,
            ];
        }
        return $slots;
    }

    public function index()
    {
        $deptId = $this->currentDept();

        $academicYear = AcademicYear::where('ay_is_active', true)->first();
        $semester     = Semester::where('sem_is_active', true)->first();

        $faculty  = Faculty::with('user')->where('fac_dept_id', $deptId)->get();
        $subjects = Subjects::where('subj_dept_id', $deptId)->where('subj_is_active', true)->get();
        $sections = Section::whereHas('program', fn ($q) => $q->where('prog_dept_id', $deptId))
            ->when($academicYear, fn ($q) => $q->where('sec_ay_id', $academicYear->ay_id))
            ->when($semester, fn ($q) => $q->where('sec_sem_id', $semester->sem_id))
            ->get();
        $rooms = Room::where('room_is_available', true)->get();

        $schedules = collect();
        if ($semester) {
            $schedules = Schedule::with(['faculty.user', 'subject', 'section', 'room'])
                ->where('sch_sem_id', $semester->sem_id)
                ->where('sch_is_active', true)
                ->whereHas('faculty', fn ($q) => $q->where('fac_dept_id', $deptId))
                ->get();
        }

        $days  = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $slots = $this->hourlySlots();

        return view('chair.schedule_plotter', compact(
            'academicYear', 'semester', 'faculty', 'subjects', 'sections',
            'rooms', 'schedules', 'days', 'slots'
        ));
    }

    public function store(Request $request)
    {
        $this->currentDept();

        $data = $request->validate([
            'subj_id'    => 'required|uuid',
            'fac_id'     => 'required|uuid',
            'sec_id'     => 'required|uuid',
            'room_id'    => 'required|uuid',
            'day'        => 'required|string',
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
        ]);

        $semester = Semester::where('sem_is_active', true)->first();
        abort_if(!$semester, 422, 'No active semester is set.');

        $faculty = Faculty::with('user')->findOrFail($data['fac_id']);
        $subject = Subjects::findOrFail($data['subj_id']);

        // ---------- HARD CONFLICT CHECK (true time-overlap, not just exact match) ----------
        $overlapBase = fn ($q) => $q->where('sch_sem_id', $semester->sem_id)
            ->where('sch_day', $data['day'])
            ->where('sch_is_active', true)
            ->where('sch_start_time', '<', $data['end_time'])
            ->where('sch_end_time', '>', $data['start_time']);

        if ($overlapBase(Schedule::where('sch_fac_id', $data['fac_id']))->exists()) {
            return back()->withInput()->withErrors([
                'conflict' => "{$faculty->user->usr_name} already has a class scheduled at this day/time.",
            ]);
        }

        if ($overlapBase(Schedule::where('sch_room_id', $data['room_id']))->exists()) {
            return back()->withInput()->withErrors([
                'conflict' => 'This room is already booked at this day/time.',
            ]);
        }

        if ($overlapBase(Schedule::where('sch_sec_id', $data['sec_id']))->exists()) {
            return back()->withInput()->withErrors([
                'conflict' => 'This section already has a class at this day/time.',
            ]);
        }

        // ---------- 30-UNIT MAX WORKLOAD CHECK ----------
        $subjectHours = $subject->subj_lecture_hours + $subject->subj_lab_hours;

        $existingHours = Study_Load::where('sl_fac_id', $data['fac_id'])
            ->where('sl_sem_id', $semester->sem_id)
            ->join('subject', 'subject.subj_id', '=', 'study_load.sl_subj_id')
            ->sum(DB::raw('subject.subj_lecture_hours + subject.subj_lab_hours'));

        $newTotal = $existingHours + $subjectHours;

        if ($newTotal > 30) {
            return back()->withInput()->withErrors([
                'conflict' => "{$faculty->user->usr_name} would exceed the 30-unit max load ({$existingHours}u + {$subjectHours}u = {$newTotal}u).",
            ]);
        }

        // ---------- SAVE — atomic, all-or-nothing ----------
        DB::transaction(function () use ($data, $semester, $newTotal) {
            $studyLoad = Study_Load::create([
                'sl_fac_id'      => $data['fac_id'],
                'sl_subj_id'     => $data['subj_id'],
                'sl_sec_id'      => $data['sec_id'],
                'sl_sem_id'      => $semester->sem_id,
                'sl_assigned_by' => Auth::id(),
            ]);

            Schedule::create([
                'sch_load_id'    => $studyLoad->sl_id,
                'sch_fac_id'     => $data['fac_id'],
                'sch_subj_id'    => $data['subj_id'],
                'sch_sec_id'     => $data['sec_id'],
                'sch_room_id'    => $data['room_id'],
                'sch_sem_id'     => $semester->sem_id,
                'sch_day'        => $data['day'],
                'sch_start_time' => $data['start_time'],
                'sch_end_time'   => $data['end_time'],
                'sch_created_by' => Auth::id(),
            ]);

            Workload::updateOrCreate(
                [
                    'wl_fac_id' => $data['fac_id'],
                    'wl_sem_id' => $semester->sem_id,
                    'wl_ay_id'  => $semester->sem_ay_id,
                ],
                ['wl_total_hours' => $newTotal]
            );
        });

        return back()->with('success', 'Class assigned successfully.');
    }

    public function destroy($id)
    {
        $this->currentDept();
        Schedule::findOrFail($id)->update(['sch_is_active' => false]);
        return back()->with('success', 'Class removed from the schedule.');
    }
}