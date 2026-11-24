<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Course;
use App\Models\Departments;
use App\Services\CourseDeletionService;
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
                // The database stores lab teaching hours, so convert them back to lab units for the course list.
                'lab'        => (float) $s->subj_lab_hours / 3,
                'units'      => (float) ($s->subj_units ?? 0),
                'total'      => (float) $s->subj_lecture_hours + ((float) $s->subj_lab_hours / 3),
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

        // Include inactive/other-program courses too: course codes are unique globally.
        $existingSubjectCodes = Course::query()
            ->pluck('course_code')
            ->filter()
            ->map(fn($code) => mb_strtoupper(trim((string) $code)))
            ->unique()
            ->values()
            ->all();

        // Keep $subject for any legacy references in the view
        $subject = $subjects;

        return view('admin.subjects', compact(
            'subject',
            'subjects',
            'subjectsForJs',
            'existingSubjectCodes',
            'departments',
            'programs',
            'yearLevels'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subj_dept_id'       => 'required|string|not_regex:/[<>]/|exists:college,college_id',
            'subj_prog_id'       => 'required|string|not_regex:/[<>]/|exists:department,dept_id',
            'subj_code'          => 'required|string|not_regex:/[<>]/|regex:/^[\p{L}\p{N}-]+$/u|unique:course,course_code',
            'subj_name'          => 'required|string|not_regex:/[<>]/|regex:/^[\p{L}\p{N}-]+$/u',
            'subj_units'         => 'required|numeric|min:0',
            'subj_lecture_units' => 'required|numeric|min:0|max:12',
            'subj_lab_units'     => 'required|numeric|min:0|max:4',
            'subj_year_level'    => 'nullable|integer|min:1|max:4',
            'subj_semester'      => 'nullable|integer|in:1,2',
        ], [
            'not_regex' => 'Angle brackets (< and >) are not allowed.',
        ]);

        try {
            Course::create([
                'course_college_id'    => $validated['subj_dept_id'],
                'course_dept_id'       => $validated['subj_prog_id'],
                'course_code'          => $validated['subj_code'],
                'course_name'          => $validated['subj_name'],
                'course_units'         => $validated['subj_units'],
                'course_lecture_hours' => $validated['subj_lecture_units'],
                'course_lab_hours'     => $validated['subj_lab_units'] * 3,
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
            'subj_dept_id'       => 'required|string|not_regex:/[<>]/|exists:college,college_id',
            'subj_prog_id'       => 'required|string|not_regex:/[<>]/|exists:department,dept_id',
            'subj_code'          => [
                'required',
                'string',
                'not_regex:/[<>]/',
                'regex:/^[\p{L}\p{N}-]+$/u',
                Rule::unique('course', 'course_code')->ignore($subject->course_id, 'course_id'),
            ],
            'subj_name'          => 'required|string|not_regex:/[<>]/|regex:/^[\p{L}\p{N}-]+$/u',
            'subj_units'         => 'required|numeric|min:0',
            'subj_lecture_units' => 'required|numeric|min:0|max:12',
            'subj_lab_units'     => 'required|numeric|min:0|max:4',
            'subj_year_level'    => 'nullable|integer|min:1|max:4',
            'subj_semester'      => 'nullable|integer|in:1,2',
        ], [
            'not_regex' => 'Angle brackets (< and >) are not allowed.',
        ]);

        try {
            $subject->update([
                'course_college_id'    => $validated['subj_dept_id'],
                'course_dept_id'       => $validated['subj_prog_id'],
                'course_code'          => $validated['subj_code'],
                'course_name'          => $validated['subj_name'],
                'course_units'         => $validated['subj_units'],
                'course_lecture_hours' => $validated['subj_lecture_units'],
                'course_lab_hours'     => $validated['subj_lab_units'] * 3,
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

    public function destroy(string $id, CourseDeletionService $courseDeletion)
    {
        $subject = Course::findOrFail($id);

        try {
            if (!$courseDeletion->deleteIfUnused($subject)) {
                return back()->with('error', 'This course is used by schedules, study loads, or archives, so it cannot be permanently deleted.');
            }
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()
                ->with('error', 'Unable to delete the subject right now. Please try again.');
        }

        return redirect()->route('subject.index')
            ->with('success', 'Course permanently deleted.');
    }
}
