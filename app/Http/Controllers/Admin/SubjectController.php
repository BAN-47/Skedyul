<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Departments;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class SubjectController extends Controller
{
    public function index()
    {
        $subject = Course::with(['department', 'program'])->latest()->paginate(5);
        $departments = College::orderBy('college_name')->get();
        $programs = Departments::orderBy('dept_name')->get();

        return view('admin.subjects', compact('subject', 'departments', 'programs'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        $departments = College::orderBy('college_name')->get();
        $programs = Departments::orderBy('dept_name')->get();

        return view('admin.create', compact('departments', 'programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subj_dept_id' => 'required|exists:college,college_id',
            'subj_prog_id' => 'required|exists:department,dept_id',
            'subj_code' => 'required|string|unique:course,course_code',
            'subj_name' => 'required|string',
            'subj_lecture_hours' => 'required|numeric|min:0',
            'subj_lab_hours' => 'required|numeric|min:0',
            'subj_is_active' => 'sometimes|boolean',
        ]);

        $validated['subj_is_active'] = $request->boolean('subj_is_active');

        try {
            Course::create($validated);
        } catch (\Throwable $e) {
            return $this->redirectWithDbError($e, 'Unable to create the subject right now. Please check your input and try again.');
        }

        return redirect()->route('subject.index')
            ->with('success', 'Subject created successfully.');
    }

    public function show(string $id)
    {
        $subject = Course::with(['department', 'program'])->findOrFail($id);
        return view('admin.show', compact('subject'));
    }

    public function edit(string $id)
    {
        $subject = Course::findOrFail($id);
        $departments = College::orderBy('college_name')->get();
        $programs = Departments::orderBy('dept_name')->get();

        return view('admin.edit', compact('subject', 'departments', 'programs'));
    }

    public function update(Request $request, string $id)
    {
        $subject = Course::findOrFail($id);

        $validated = $request->validate([
            'subj_dept_id' => 'required|exists:college,college_id',
            'subj_prog_id' => 'required|exists:department,dept_id',
            'subj_code' => [
                'required',
                'string',
                Rule::unique('course', 'course_code')->ignore($subject->course_id, 'course_id'),
            ],
            'subj_name' => 'required|string',
            'subj_lecture_hours' => 'required|numeric|min:0',
            'subj_lab_hours' => 'required|numeric|min:0',
            'subj_is_active' => 'sometimes|boolean',
        ]);

        $validated['subj_is_active'] = $request->boolean('subj_is_active');

        try {
            $subject->update($validated);
        } catch (\Throwable $e) {
            return $this->redirectWithDbError($e, 'Unable to update the subject right now. Please try again.');
        }

        return redirect()->route('subject.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy(string $id)
    {
        $subject = Course::findOrFail($id);

        try {
            $subject->delete();
        } catch (\Throwable $e) {
            return $this->redirectWithDbError($e, 'Cannot delete this subject because it is still assigned in another record.');
        }

        return redirect()->route('subject.index')
            ->with('success', 'Subject deleted successfully.');
    }
}
