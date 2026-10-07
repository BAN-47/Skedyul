<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\Faculty;
use App\Models\Study_Load;
use App\Models\Semester;
use App\Models\Dept_Chair;
use App\Models\Notification;
use Illuminate\Http\Request;

class ScheduleReportsController extends Controller
{
    public function index()
    {
        $activeSemester = Semester::where('sem_is_active', true)->first();

        $byDepartment = Schedule::with(['section.program.department', 'subject', 'faculty.user', 'room'])
            ->where('sch_is_active', true)
            ->get();

        $faculty = Faculty::get();
        $loadsByFaculty = Study_Load::with('subject')
            ->when($activeSemester, fn($q) => $q->where('sl_sem_id', $activeSemester->sem_id))
            ->get()
            ->groupBy('sl_fac_id');

        $byFaculty = $faculty->map(function ($fac) use ($loadsByFaculty) {
            $loads = $loadsByFaculty->get($fac->fac_id, collect());
            $subjects = $loads->pluck('subject.course_code')->filter()->unique()->implode(', ');
            $hours = $loads->sum(fn ($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject));

            return ['name' => $fac->full_name, 'subjects' => $subjects, 'hours' => $hours];
        });

        $sections = Section::with('program')->get();

        $chairs = Dept_Chair::with('department')->get();

        return view('dean.schedule_reports', compact('byDepartment', 'byFaculty', 'sections', 'chairs'));
    }

    public function departmentDetail(string $deptId)
    {
        $activeSemester = Semester::where('sem_is_active', true)->first();

        $facultyRecords = Faculty::where('fac_college_id', $deptId)->get();
        $facultyIds = $facultyRecords->pluck('fac_id');

        $loadsByFaculty = Study_Load::with('subject')
            ->whereIn('sl_fac_id', $facultyIds)
            ->when($activeSemester, fn($q) => $q->where('sl_sem_id', $activeSemester->sem_id))
            ->get()
            ->groupBy('sl_fac_id');

        $faculty = $facultyRecords->map(function ($fac) use ($loadsByFaculty) {
            $hours = $loadsByFaculty->get($fac->fac_id, collect())
                ->sum(fn ($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject));
            $maxHours = \App\Services\ScheduleAssignmentService::facultyMaxHours($fac);
            return [
                'name'       => $fac->full_name,
                'rank'       => $fac->fac_rank,
                'employment' => $fac->fac_employment_type,
                'load'       => $hours . 'h',
                'status'     => $hours > $maxHours ? 'Overload' : ($hours >= $maxHours - 3 ? 'Near Max' : 'OK'),
            ];
        });

        $sections = Section::whereHas('program', fn($q) => $q->where('dept_college_id', $deptId))->get();

        return response()->json([
            'faculty'  => $faculty,
            'sections' => $sections->count(),
        ]);
    }

    public function detectConflicts(string $sectionId = null)
    {
        $schedules = Schedule::with(['faculty', 'subject', 'room'])
            ->where('sch_is_active', true)
            ->when($sectionId, fn($q) => $q->where('sch_sec_id', $sectionId))
            ->get();

        $conflicts = [];
        $seen = [];

        foreach ($schedules->groupBy('sch_fac_id') as $group) {
            foreach ($group as $a) {
                foreach ($group as $b) {
                    if ($a->sch_id === $b->sch_id || $a->sch_day !== $b->sch_day) continue;

                    $overlap = $a->sch_start_time < $b->sch_end_time && $b->sch_start_time < $a->sch_end_time;
                    if (!$overlap) continue;

                    $pairKey = collect([$a->sch_id, $b->sch_id])->sort()->implode('-');
                    if (isset($seen[$pairKey])) continue;
                    $seen[$pairKey] = true;

                    $conflicts[] = [
                        'faculty'   => $a->faculty->full_name ?? 'Unknown',
                        'subject_a' => $a->subject->subj_code ?? '',
                        'subject_b' => $b->subject->subj_code ?? '',
                        'day'       => $a->sch_day,
                        'time'      => $a->sch_start_time . '–' . $a->sch_end_time,
                        'room_a'    => $a->room->room_name ?? '',
                        'room_b'    => $b->room->room_name ?? '',
                    ];
                }
            }
        }

        return response()->json($conflicts);
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'message'       => 'required|string',
            'type'          => 'required|in:info,reminder,urgent,deadline',
            'recipients'    => 'required|array|min:1',
            'recipients.*'  => 'exists:USER,usr_id',
        ]);

        foreach ($request->recipients as $usrId) {
            Notification::create([
                'notif_usr_id'  => $usrId,
                'notif_title'   => $request->title,
                'notif_message' => $request->message,
                'notif_type'    => $request->type,
                'notif_is_read' => false,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
