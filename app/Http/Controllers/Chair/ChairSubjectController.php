<?php 

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Subjects;
use App\Models\Schedule;
use App\Models\Program;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChairSubjectController extends Controller {

    public function index()
    {
        $subjects = Subjects::with(['department', 'program'])
            ->where('subj_is_active', true)
            ->get()
            ->map(function ($s) {
                $schedule = Schedule::where('sch_subj_id', $s->subj_id)
                    ->where('sch_is_active', true)
                    ->first();

                $s->assignedFaculty = $schedule?->sch_fac_id
                    ? optional($schedule->faculty)->fac_name ?? 'Assigned'
                    : null;

                return $s;
            });

        $departments = Department::orderBy('dept_name')->get();
        $programs = Program::orderBy('prog_name')->get();
        $section = Section::orderBy('sec_name')->get();

        return view('chair.subjects', compact('subjects', 'departments', 'programs', 'section'));
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

        Subjects::create($validated);

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

        $subject->update($validated);

        return redirect()->route('chair.subjects')
            ->with('success', 'Subject updated successfully.');

    }

    public function destroy(string $id)
    {
        $subject = Subjects::findOrFail($id);
        $subject->update(['subj_is_active' => false]);

        return redirect()->route('chair.subjects')
            ->with('success', 'Subject deactivated successfully.');
    }

}

?>