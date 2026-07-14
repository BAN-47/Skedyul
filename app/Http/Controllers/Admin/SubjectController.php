<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Admin\Program;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index()
    {
        $subject = Subjects::with(['department', 'program'])->latest()->paginate(5);
        $department = Department::orderBy('dept_name')->get();
        $program = Program::orderBy('prog_name')->get();

        return view('admin.subject', compact('subject', 'department', 'program'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        $department = Department::orderBy('dept_name')->get();
        $program = Program::orderBy('prog_name')->get();

        return view('admin.create', compact('department', 'program'));
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

        Subjects::create($validated);

        return redirect()->route('subject.index')
            ->with('success', 'Subject created successfully.');
    }

    public function show(string $id)
    {
        $subject = Subjects::with(['department', 'program'])->findOrFail($id);
        return view('admin.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $subject = Subjects::findOrFail($id);
        $department = Department::orderBy('dept_name')->get();
        $program = Program::orderBy('prog_name')->get();

        return view('admin.edit', compact('subject', 'department', 'program'));
    }

    /**
     * Update the specified resource in storage.
     */
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

        $subject->update($validated);

        return redirect()->route('subject.index')
            ->with('success', 'Subject updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subject = Subjects::findOrFail($id);
        $subject->delete();

        return redirect()->route('subject.index')
            ->with('success', 'Subject deleted successfully.');
    }
}