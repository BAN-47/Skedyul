<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Study_Load;
use App\Models\Course;
use App\Models\Departments;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ChairSubjectController extends Controller {

    public function index()
    {
        $deptChair = Dept_Chair::where('dc_usr_id', Auth::id())->first();

        $subjects = Course::with(['college', 'department'])
            ->where('course_is_active', true)
            ->get();

        $subjectIds = $subjects->pluck('course_id');

        /*
         * "Assigned" now means a Study_Load exists for the subject (faculty
         * + section + semester, chosen right here on this page) - it no
         * longer requires an actual time-slot Schedule row, since plotting
         * the day/time/room now happens on PBS, not here.
         */
        $assignedLoads = Study_Load::with('faculty.user')
            ->whereIn('sl_course_id', $subjectIds)
            ->get()
            ->groupBy('sl_course_id');

        $subjects = $subjects->map(function ($subject) use ($assignedLoads) {
            $load = $assignedLoads->get($subject->course_id)?->first();

            $subject->assignedFaculty = $load && $load->faculty
                ? ($load->faculty->user->usr_name ?? $load->faculty->full_name ?? 'Assigned')
                : null;

            return $subject;
        });

        $colleges    = College::orderBy('college_name')->get();
        $departments = Departments::orderBy('dept_name')->get();
        $section = Section::orderBy('sec_name')->get();
        // Join to academic_year so the dropdown can show "2026-2027 1st Sem"
        // instead of just "First Semester" with no year context.
        $semesters = DB::table('semester')
            ->join('academic_year', 'semester.sem_ay_id', '=', 'academic_year.ay_id')
            ->select('semester.*', 'academic_year.ay_academic_year')
            ->orderBy('academic_year.ay_academic_year', 'desc')
            ->orderBy('semester.sem_start_date', 'desc')
            ->get()
            ->map(function ($sem) {
                $ordinal = match (true) {
                    str_contains(strtolower($sem->sem_name), 'first')  => '1st Sem',
                    str_contains(strtolower($sem->sem_name), 'second') => '2nd Sem',
                    str_contains(strtolower($sem->sem_name), 'third')  => '3rd Sem',
                    str_contains(strtolower($sem->sem_name), 'summer') => 'Summer',
                    default => $sem->sem_name,
                };
                $sem->sem_label = "{$sem->ay_academic_year} {$ordinal}";
                return $sem;
            });

        // ---------- Data the Assign Faculty modal needs ----------
        // Scoped to the logged-in chair's own department so you can't
        // accidentally assign a subject to faculty from another department.
        $faculty = collect();
        if ($deptChair) {
            $facultyRecords = Faculty::where('fac_college_id', $deptChair->dc_college_id)
                ->orderBy('fac_first_name')
                ->get();

            $facultyIds = $facultyRecords->pluck('fac_id');

            $studyLoads = Study_Load::whereIn('sl_fac_id', $facultyIds)->get()->groupBy('sl_fac_id');
            $subjectsById = $subjects->keyBy('course_id');

            $faculty = $facultyRecords->map(function (Faculty $f) use ($studyLoads, $subjectsById) {
                $totalUnits = $studyLoads->get($f->fac_id, collect())->sum(function ($sl) use ($subjectsById) {
                    $s = $subjectsById->get($sl->sl_course_id);
                    return $s ? ((float) $s->course_lecture_hours + (float) $s->course_lab_hours) : 0;
                });

                return [
                    'id'    => $f->fac_id,
                    'name'  => trim("{$f->fac_first_name} {$f->fac_last_name}"),
                    'units' => $totalUnits,
                ];
            });
        }

        return view('chair.subjects', compact('subjects', 'colleges', 'departments', 'section', 'faculty', 'semesters'));
    }

    public function store(Request $request)
    {
        // Prefer course_* names; also accept legacy subj_* from older blades
        $request->merge([
            'course_college_id'    => $request->input('course_college_id', $request->input('subj_dept_id')),
            'course_dept_id'       => $request->input('course_dept_id', $request->input('subj_prog_id')),
            'course_code'          => $request->input('course_code', $request->input('subj_code')),
            'course_name'          => $request->input('course_name', $request->input('subj_name')),
            'course_lecture_hours' => $request->input('course_lecture_hours', $request->input('subj_lecture_hours')),
            'course_lab_hours'     => $request->input('course_lab_hours', $request->input('subj_lab_hours')),
        ]);

        $data = $request->validate([
            'course_college_id'    => 'required|uuid|exists:college,college_id',
            'course_dept_id'       => 'required|uuid|exists:department,dept_id',
            'course_code'          => 'required|string|unique:course,course_code',
            'course_name'          => 'required|string',
            'course_lecture_hours' => 'required|numeric|min:0',
            'course_lab_hours'     => 'required|numeric|min:0',
        ]);

        try {
            Course::create(array_merge($data, ['course_is_active' => true]));
        } catch (\Throwable $e) {
            return $this->redirectWithDbError($e, 'Unable to add the course right now. Please try again.');
        }

        return redirect()->route('chair.subjects')
            ->with('success', 'Course added successfully.');
    }

    public function update(Request $request, string $id)
    {
        $course = Course::findOrFail($id);

        $request->merge([
            'course_college_id'    => $request->input('course_college_id', $request->input('subj_dept_id')),
            'course_dept_id'       => $request->input('course_dept_id', $request->input('subj_prog_id', $course->course_dept_id)),
            'course_code'          => $request->input('course_code', $request->input('subj_code')),
            'course_name'          => $request->input('course_name', $request->input('subj_name')),
            'course_lecture_hours' => $request->input('course_lecture_hours', $request->input('subj_lecture_hours')),
            'course_lab_hours'     => $request->input('course_lab_hours', $request->input('subj_lab_hours')),
        ]);

        $data = $request->validate([
            'course_college_id'    => 'required|uuid|exists:college,college_id',
            'course_dept_id'       => 'nullable|uuid|exists:department,dept_id',
            'course_code'          => [
                'required',
                'string',
                Rule::unique('course', 'course_code')->ignore($course->course_id, 'course_id'),
            ],
            'course_name'          => 'required|string',
            'course_lecture_hours' => 'required|numeric|min:0',
            'course_lab_hours'     => 'required|numeric|min:0',
        ]);

        try {
            $course->update($data);
        } catch (\Throwable $e) {
            return $this->redirectWithDbError($e, 'Unable to update the course right now. Please try again.');
        }

        return redirect()->route('chair.subjects')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(string $id)
    {
        $course = Course::findOrFail($id);

        try {
            $course->update(['course_is_active' => false]);
        } catch (\Throwable $e) {
            return $this->redirectWithDbError($e, 'Unable to deactivate the course right now. Please try again.');
        }

        return redirect()->route('chair.subjects')
            ->with('success', 'Course deactivated successfully.');
    }


    private function redirectWithDbError(\Throwable $e, string $fallback)
    {
        report($e);
        return redirect()->back()->with('error', $fallback);
    }

}
