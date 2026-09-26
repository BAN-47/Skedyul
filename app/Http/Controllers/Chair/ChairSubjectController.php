<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Room;
use App\Models\Study_Load;
use App\Models\Subjects;
use App\Models\Schedule;
use App\Models\Program;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ChairSubjectController extends Controller {

    public function index()
    {
        $deptChair = Dept_Chair::where('dc_usr_id', Auth::id())->first();

        $subjects = Subjects::with(['department', 'program'])
            ->where('subj_is_active', true)
            ->get();

        $subjectIds = $subjects->pluck('subj_id');

        $assignedSchedules = Schedule::with(['faculty.user'])
            ->whereIn('sch_subj_id', $subjectIds)
            ->where('sch_is_active', true)
            ->get()
            ->groupBy('sch_subj_id');

        $subjects = $subjects->map(function ($subject) use ($assignedSchedules) {
            $schedule = $assignedSchedules->get($subject->subj_id)?->first();

            $subject->assignedFaculty = $schedule && $schedule->sch_fac_id
                ? ($schedule->faculty->user->usr_name ?? $schedule->faculty->full_name ?? 'Assigned')
                : null;

            return $subject;
        });

        $departments = Department::orderBy('dept_name')->get();
        $programs = Program::orderBy('prog_name')->get();
        $section = Section::orderBy('sec_name')->get();

        // ---------- Data the Assign Faculty modal needs ----------
        // Scoped to the logged-in chair's own department so you can't
        // accidentally assign a subject to faculty from another department.
        $faculty = collect();
        if ($deptChair) {
            $facultyRecords = Faculty::where('fac_dept_id', $deptChair->dc_dept_id)
                ->orderBy('fac_first_name')
                ->get();

            $facultyIds = $facultyRecords->pluck('fac_id');

            $studyLoads = Study_Load::whereIn('sl_fac_id', $facultyIds)->get()->groupBy('sl_fac_id');
            $subjectsById = $subjects->keyBy('subj_id');

            $faculty = $facultyRecords->map(function (Faculty $f) use ($studyLoads, $subjectsById) {
                $totalUnits = $studyLoads->get($f->fac_id, collect())->sum(function ($sl) use ($subjectsById) {
                    $s = $subjectsById->get($sl->sl_subj_id);
                    return $s ? ((float) $s->subj_lecture_hours + (float) $s->subj_lab_hours) : 0;
                });

                return [
                    'id'    => $f->fac_id,
                    'name'  => trim("{$f->fac_first_name} {$f->fac_last_name}"),
                    'units' => $totalUnits,
                ];
            });
        }

        $rooms = Room::where('room_is_available', true)->orderBy('room_name')->get();

        return view('chair.subjects', compact('subjects', 'departments', 'programs', 'section', 'faculty', 'rooms'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'subj_dept_id' => 'required|exists:department,dept_id',
            'subj_prog_id' => 'required|exists:program,prog_id',
            'subj_code' => 'required|string|unique:subject,subj_code',
            'subj_name' => 'required|string',
            'subj_lecture_hours' => 'required|numeric|min:0',
            'subj_lab_hours' => 'required|numeric|min:0',
        ]);

        try {
            Subjects::create($validated);
        } catch (\Throwable $e) {
            return $this->redirectWithDbError($e, 'Unable to add the subject right now. Please try again.');
        }

        return redirect()->route('chair.subjects')
            ->with('success', 'Subject added successfully.');
    }

    public function update(Request $request, string $id) {
        $subject = Subjects::findOrFail($id);

        $validated = $request->validate([
            'subj_dept_id' => 'required|exists:department,dept_id',
            'subj_code' => [
                'required',
                'string',
                Rule::unique('subject', 'subj_code')->ignore($subject->subj_id, 'subj_id'),
            ],
            'subj_name' => 'required|string',
            'subj_lecture_hours' => 'required|numeric|min:0',
            'subj_lab_hours' => 'required|numeric|min:0',
        ]);

        try {
            $subject->update($validated);
        } catch (\Throwable $e) {
            return $this->redirectWithDbError($e, 'Unable to update the subject right now. Please try again.');
        }

        return redirect()->route('chair.subjects')
            ->with('success', 'Subject updated successfully.');

    }

    public function destroy(string $id)
    {
        $subject = Subjects::findOrFail($id);

        try {
            $subject->update(['subj_is_active' => false]);
        } catch (\Throwable $e) {
            return $this->redirectWithDbError($e, 'Unable to deactivate the subject right now. Please try again.');
        }

        return redirect()->route('chair.subjects')
            ->with('success', 'Subject deactivated successfully.');
    }

}