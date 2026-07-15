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

        Subjects::create($validated);

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

        $subject->update($validated);

        return redirect()->route('subject.index')
            ->with('success', 'Subject updated successfully.');
    }

   public function destroy(string $id)
    {
        $subject = Subjects::findOrFail($id);

        try {
            $subject->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23503') {
                return redirect()->route('subject.index')
                    ->with('error', 'Cannot delete this subject — it is still assigned in one or more study loads.');
            }

            throw $e;
        }

        return redirect()->route('subject.index')
            ->with('success', 'Subject deleted successfully.');
    }
}