<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Program;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class SubjectController extends Controller
{
    public function index()
    {
        $subject = Subjects::with(['department', 'program'])->latest()->paginate(5);
        $departments = Department::orderBy('dept_name')->get();
        $programs = Program::orderBy('prog_name')->get();

        return view('admin.subjects', compact('subject', 'departments', 'programs'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        $departments = Department::orderBy('dept_name')->get();
        $programs = Program::orderBy('prog_name')->get();

        return view('admin.create', compact('departments', 'programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subj_dept_id' => 'required|exists:department,dept_id',
            'subj_prog_id' => 'required|exists:program,prog_id',
            'subj_code' => 'required|string|unique:subject,subj_code',
            'subj_name' => 'required|string',
            'subj_lecture_hours' => 'required|numeric|min:0',
            'subj_lab_hours' => 'required|numeric|min:0',
            'subj_is_active' => 'sometimes|boolean',
        ]);

        $validated['subj_is_active'] = $request->boolean('subj_is_active');

        try {
            Subjects::create($validated);
        } catch (\Throwable $e) {
            return $this->redirectWithDbError($e, 'Unable to create the subject right now. Please check your input and try again.');
        }

        return redirect()->route('subject.index')
            ->with('success', 'Subject created successfully.');
    }

    public function show(string $id)
    {
        $subject = Subjects::with(['department', 'program'])->findOrFail($id);
        return view('admin.show', compact('subject'));
    }

    public function edit(string $id)
    {
        $subject = Subjects::findOrFail($id);
        $departments = Department::orderBy('dept_name')->get();
        $programs = Program::orderBy('prog_name')->get();

        return view('admin.edit', compact('subject', 'departments', 'programs'));
    }

    public function update(Request $request, string $id)
    {
        $subject = Subjects::findOrFail($id);

        $validated = $request->validate([
            'subj_dept_id' => 'required|exists:department,dept_id',
            'subj_prog_id' => 'required|exists:program,prog_id',
            'subj_code' => [
                'required',
                'string',
                Rule::unique('subject', 'subj_code')->ignore($subject->subj_id, 'subj_id'),
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
        $subject = Subjects::findOrFail($id);

        try {
            $subject->delete();
        } catch (\Throwable $e) {
            return $this->redirectWithDbError($e, 'Cannot delete this subject because it is still assigned in another record.');
        }

        return redirect()->route('subject.index')
            ->with('success', 'Subject deleted successfully.');
    }
}