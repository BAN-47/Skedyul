<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Course;
use App\Models\Departments;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    /**
     * Admin manages curriculum for BIT-CT, BSIS, and BSIT.
     * Filter by program → year level → semester. Add / Edit / Delete only
     * (no faculty assignment — that stays on the chair side).
     */
    public function index()
    {
        // Target programs only
        $programs = Departments::query()
            ->where(function ($q) {
                $q->whereRaw("upper(coalesce(dept_code, '')) IN ('BSIS', 'BSIT', 'BIT-CT', 'BIT CT', 'BITCT')")
                    ->orWhere('dept_name', 'ilike', '%Information Systems%')
                    ->orWhere('dept_name', 'ilike', '%Information Technology%')
                    ->orWhere('dept_name', 'ilike', '%Computer Technology%')
                    ->orWhere('dept_name', 'ilike', '%BIT%');
            })
            ->orderBy('dept_name')
            ->get();

        // Fallback: all programs if none matched the name filters
        if ($programs->isEmpty()) {
            $programs = Departments::orderBy('dept_name')->get();
        }

        $programIds = $programs->pluck('dept_id');

        $subjects = Course::with(['department', 'program'])
            ->where(function ($q) {
                $q->where('course_is_active', true)->orWhereNull('course_is_active');
            })
            ->when($programIds->isNotEmpty(), fn($q) => $q->whereIn('course_dept_id', $programIds))
            ->orderBy('course_code')
            ->get();

        $departments = College::orderBy('college_name')->get();

        $yearLevels = [
            1 => '1st Year',
            2 => '2nd Year',
            3 => '3rd Year',
            4 => '4th Year',
        ];

        // Flat list for client-side filtering (program + year + semester)
        $subjectsForJs = $subjects->map(function ($s) {
            $attrs = $s->getAttributes();
            $year  = $attrs['course_year_level'] ?? null;
            $sem   = $attrs['course_semester'] ?? null;

            return [
                'id'         => $s->subj_id,
                'code'       => $s->subj_code,
                'name'       => $s->subj_name,
                'lec'        => (float) $s->subj_lecture_hours,
                'lab'        => (float) $s->subj_lab_hours,
                'units'      => (float) $s->subj_lecture_hours + (float) $s->subj_lab_hours,
                'dept_id'    => $s->subj_dept_id,
                'prog_id'    => $s->subj_prog_id,
                'dept_name'  => optional($s->department)->college_name
                    ?? optional($s->department)->dept_name
                    ?? '—',
                'prog_name'  => optional($s->program)->dept_name
                    ?? optional($s->program)->prog_name
                    ?? '—',
                'year_level' => $year !== null && $year !== '' ? (int) $year : null,
                'semester'   => $sem !== null && $sem !== '' ? (int) $sem : null,
            ];
        })->values()->all();

        // Keep $subject for any legacy references in the view
        $subject = $subjects;

        return view('admin.subjects', compact(
            'subject',
            'subjects',
            'subjectsForJs',
            'departments',
            'programs',
            'yearLevels'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subj_dept_id'       => 'required|exists:college,college_id',
            'subj_prog_id'       => 'required|exists:department,dept_id',
            'subj_code'          => 'required|string|unique:course,course_code',
            'subj_name'          => 'required|string',
            'subj_lecture_hours' => 'required|numeric|min:0',
            'subj_lab_hours'     => 'required|numeric|min:0',
            'subj_year_level'    => 'nullable|integer|min:1|max:4',
            'subj_semester'      => 'nullable|integer|in:1,2',
        ]);

        try {
            Course::create([
                'course_college_id'    => $validated['subj_dept_id'],
                'course_dept_id'       => $validated['subj_prog_id'],
                'course_code'          => $validated['subj_code'],
                'course_name'          => $validated['subj_name'],
                'course_lecture_hours' => $validated['subj_lecture_hours'],
                'course_lab_hours'     => $validated['subj_lab_hours'],
                'course_year_level'    => $validated['subj_year_level'] ?? null,
                'course_semester'      => $validated['subj_semester'] ?? null,
                'course_is_active'     => true,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Unable to add the subject right now. Please try again.');
        }

        return redirect()->route('subject.index')
            ->with('success', 'Subject added successfully.');
    }

    public function update(Request $request, string $id)
    {
        $subject = Course::findOrFail($id);

        $validated = $request->validate([
            'subj_dept_id'       => 'required|exists:college,college_id',
            'subj_prog_id'       => 'required|exists:department,dept_id',
            'subj_code'          => [
                'required',
                'string',
                Rule::unique('course', 'course_code')->ignore($subject->course_id, 'course_id'),
            ],
            'subj_name'          => 'required|string',
            'subj_lecture_hours' => 'required|numeric|min:0',
            'subj_lab_hours'     => 'required|numeric|min:0',
            'subj_year_level'    => 'nullable|integer|min:1|max:4',
            'subj_semester'      => 'nullable|integer|in:1,2',
        ]);

        try {
            $subject->update([
                'course_college_id'    => $validated['subj_dept_id'],
                'course_dept_id'       => $validated['subj_prog_id'],
                'course_code'          => $validated['subj_code'],
                'course_name'          => $validated['subj_name'],
                'course_lecture_hours' => $validated['subj_lecture_hours'],
                'course_lab_hours'     => $validated['subj_lab_hours'],
                'course_year_level'    => $validated['subj_year_level'] ?? null,
                'course_semester'      => $validated['subj_semester'] ?? null,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Unable to update the subject right now. Please try again.');
        }

        return redirect()->route('subject.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy(string $id)
    {
        $subject = Course::findOrFail($id);

        try {
            $subject->update(['course_is_active' => false]);
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()
                ->with('error', 'Unable to delete the subject right now. Please try again.');
        }

        return redirect()->route('subject.index')
            ->with('success', 'Subject deleted successfully.');
    }
}
