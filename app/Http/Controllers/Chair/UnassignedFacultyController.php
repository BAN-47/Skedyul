<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Schedule_Submission;
use App\Models\Semester;
use App\Services\FacultyNameFormatter;
use App\Services\ScheduleAssignmentService;
use App\Services\UnassignedFacultyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnassignedFacultyController extends Controller
{
    public function index()
    {
        $chair = Dept_Chair::with('program')
            ->where('dc_usr_id', Auth::id())
            ->firstOrFail();
        abort_if(!$chair->dc_dept_id, 403, 'Your account is not assigned to a department.');

        $semester = Semester::with('academicYear')->where('sem_is_active', true)->first();
        $schedules = Schedule::with(['subject', 'section', 'room', 'semester.academicYear'])
            ->where('sch_fac_id', UnassignedFacultyService::FACULTY_ID)
            ->where('sch_fac_assignment_dept_id', $chair->dc_dept_id)
            ->where('sch_is_active', true)
            ->when($semester, fn ($query) => $query->where('sch_sem_id', $semester->sem_id))
            ->orderBy('sch_day')
            ->orderBy('sch_start_time')
            ->get();

        $faculty = Faculty::with('user')
            ->where('fac_dept_id', $chair->dc_dept_id)
            ->where('fac_is_placeholder', false)
            ->whereHas('user', fn ($query) => $query->where('usr_is_active', true))
            ->orderBy('fac_last_name')
            ->orderBy('fac_first_name')
            ->get();

        return view('chair.unassigned_faculty', [
            'chair' => $chair,
            'semester' => $semester,
            'schedules' => $schedules,
            'faculty' => $faculty,
        ]);
    }

    public function assign(Request $request, string $id, ScheduleAssignmentService $scheduler)
    {
        $chair = Dept_Chair::where('dc_usr_id', Auth::id())->firstOrFail();
        abort_if(!$chair->dc_dept_id, 403, 'Your account is not assigned to a department.');

        $data = $request->validate([
            'fac_id' => ['required', 'uuid', 'exists:faculty,fac_id'],
        ]);

        $schedule = Schedule::with(['subject', 'section', 'room'])
            ->whereKey($id)
            ->where('sch_fac_id', UnassignedFacultyService::FACULTY_ID)
            ->where('sch_fac_assignment_dept_id', $chair->dc_dept_id)
            ->where('sch_is_active', true)
            ->firstOrFail();

        $semesterIsCurrent = Semester::whereKey($schedule->sch_sem_id)
            ->where('sem_is_active', true)
            ->exists();
        if (!$semesterIsCurrent) {
            return back()->with('error', 'Only schedules in the current semester can be assigned here.');
        }

        $faculty = Faculty::query()
            ->whereKey($data['fac_id'])
            ->where('fac_dept_id', $chair->dc_dept_id)
            ->where('fac_is_placeholder', false)
            ->whereHas('user', fn ($query) => $query->where('usr_is_active', true))
            ->first();
        if (!$faculty) {
            return back()->with('error', 'Choose an active faculty member from your department.');
        }

        $assignment = [
            'fac_id' => $faculty->fac_id,
            'subj_id' => $schedule->sch_course_id,
            'sec_id' => $schedule->sch_sec_id,
            'room_id' => $schedule->sch_room_id,
            'sem_id' => $schedule->sch_sem_id,
            'day' => $schedule->sch_day,
            'shift' => $schedule->sch_shift,
            'start_time' => $schedule->sch_start_time,
            'end_time' => $schedule->sch_end_time,
        ];

        $result = DB::transaction(function () use ($scheduler, $schedule, $assignment, $faculty) {
            $updated = $scheduler->update((string) $schedule->sch_id, $assignment);
            if (!$updated['success']) {
                return $updated;
            }

            // Keep Dean's submitted PBS view in sync if this class was already sent.
            $submissions = Schedule_Submission::query()
                ->where('schsub_type', 'pbs')
                ->where('schsub_sem_id', $schedule->sch_sem_id)
                ->whereIn('schsub_status', ['pending', 'approved', 'returned'])
                ->get();
            foreach ($submissions as $submission) {
                $snapshot = collect($submission->schsub_schedule_snapshot ?? []);
                $changed = false;
                $snapshot = $snapshot->map(function ($row) use ($schedule, $faculty, &$changed) {
                    if ((string) ($row['schedule_id'] ?? '') !== (string) $schedule->sch_id) {
                        return $row;
                    }
                    $row['faculty'] = FacultyNameFormatter::format($faculty);
                    $changed = true;
                    return $row;
                });
                if ($changed) {
                    $submission->schsub_schedule_snapshot = $snapshot->values()->all();
                    $submission->save();
                }
            }

            return $updated;
        });

        if (!$result['success']) {
            return back()->with('error', $result['message'] ?? 'The faculty member could not be assigned.');
        }

        return back()->with('success', 'The class is now assigned to ' . FacultyNameFormatter::format($faculty) . '. Their workload and conflict checks now apply.');
    }
}
