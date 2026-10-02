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

class ChairSubjectController extends Controller
{

    public function index()
    {
        $deptChair = Dept_Chair::where('dc_usr_id', Auth::id())->first();

        // Scope to the logged-in chair's program (e.g. BSIS)
        $collegeId = $deptChair?->dc_college_id;
        $programId = $deptChair?->dc_dept_id ?? $deptChair?->dc_prog_id;

        $program = $programId ? Departments::find($programId) : null;
        $programName = $program?->dept_name ?? $program?->prog_name ?? 'Department';

        // Collect every department id that represents this program.
        // Seed may have attached courses to a matching dept row that differs
        // from the chair's dc_dept_id (e.g. code/name match, different uuid).
        $programDeptIds = collect([$programId])->filter();

        if ($program) {
            $related = Departments::query()
                ->where(function ($q) use ($program, $collegeId) {
                    $q->where('dept_id', $program->dept_id);

                    if (! empty($program->dept_code)) {
                        $q->orWhereRaw('upper(coalesce(dept_code, \'\')) = ?', [strtoupper($program->dept_code)]);
                    }

                    if (! empty($program->dept_name)) {
                        $q->orWhere('dept_name', $program->dept_name);
                        // BSIS / Information Systems name variants
                        if (
                            stripos($program->dept_name, 'Information Systems') !== false
                            || stripos($program->dept_name, 'BSIS') !== false
                        ) {
                            $q->orWhere('dept_name', 'ilike', '%Information Systems%')
                                ->orWhereRaw('upper(coalesce(dept_code, \'\')) = ?', ['BSIS']);
                        }
                    }
                })
                ->pluck('dept_id');

            $programDeptIds = $programDeptIds->merge($related)->unique()->values();
        }

        // Prefer program-scoped courses. Do NOT also require course_college_id
        // to match — seeded rows use the program's college, which can differ
        // from department_chair.dc_college_id.
        $subjects = Course::with(['department', 'program'])
            ->where(function ($q) {
                $q->where('course_is_active', true)
                    ->orWhereNull('course_is_active');
            })
            ->when($programDeptIds->isNotEmpty(), function ($q) use ($programDeptIds) {
                $q->whereIn('course_dept_id', $programDeptIds->all());
            }, function ($q) use ($collegeId) {
                // Fallback: college only if no program ids resolved
                if ($collegeId) {
                    $q->where('course_college_id', $collegeId);
                }
            })
            ->orderBy('course_code')
            ->get();

        $subjectIds = $subjects->pluck('course_id');

        /*
         * "Assigned" means a Study_Load exists for the subject (faculty
         * + section + semester). Plotting day/time/room happens on PBS.
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

        $departments = College::orderBy('college_name')->get();
        $programs = Departments::orderBy('dept_name')->get();

        // Sections for this program (same dept-id set used for subjects)
        $section = Section::query()
            ->when($programDeptIds->isNotEmpty(), function ($q) use ($programDeptIds) {
                $q->whereIn('sec_dept_id', $programDeptIds->all());
            }, function ($q) use ($programId, $collegeId) {
                if ($programId) {
                    $q->where('sec_dept_id', $programId);
                }
            })
            ->orderBy('sec_year_level')
            ->orderBy('sec_name')
            ->get();

        // Join to academic_year so the dropdown can show "2026-2027 1st Sem"
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
                $sem->sem_ordinal = $ordinal; // for simple 1st/2nd filter matching
                return $sem;
            });

        // Faculty scoped to chair's college (and program when available)
        $faculty = collect();
        if ($deptChair) {
            $facultyRecords = Faculty::where('fac_college_id', $deptChair->dc_college_id)
                ->when($programId, fn($q) => $q->where('fac_dept_id', $programId))
                ->orderBy('fac_first_name')
                ->get();

            $facultyIds = $facultyRecords->pluck('fac_id');

            $studyLoads = Study_Load::whereIn('sl_fac_id', $facultyIds)->get()->groupBy('sl_fac_id');
            $subjectsById = $subjects->keyBy('course_id');

            $faculty = $facultyRecords->map(function (Faculty $f) use ($studyLoads, $subjectsById) {
                $totalUnits = $studyLoads->get($f->fac_id, collect())->sum(function ($sl) use ($subjectsById) {
                    $s = $subjectsById->get($sl->sl_course_id);
                    return $s ? ((float) $s->subj_lecture_hours + (float) $s->subj_lab_hours) : 0;
                });

                return [
                    'id'    => $f->fac_id,
                    'name'  => trim("{$f->fac_first_name} {$f->fac_last_name}"),
                    'units' => $totalUnits,
                ];
            });
        }

        $yearLevels = [
            1 => '1st Year',
            2 => '2nd Year',
            3 => '3rd Year',
            4 => '4th Year',
        ];

        // Plain array for JS — avoid multi-line @json + arrow fn in Blade (parse error)
        // Read year/semester from raw attributes so legacy getAttribute aliases can't hide them.
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
                'faculty'    => $s->assignedFaculty,
                'dept_id'    => $s->subj_dept_id,
                'year_level' => $year !== null && $year !== '' ? (int) $year : null,
                'semester'   => $sem !== null && $sem !== '' ? (int) $sem : null,
            ];
        })->values()->all();

        return view('chair.subjects', compact(
            'subjects',
            'subjectsForJs',
            'departments',
            'programs',
            'section',
            'faculty',
            'semesters',
            'yearLevels',
            'programName'
        ));
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
        ]);

        try {
            Course::create($validated);
        } catch (\Throwable $e) {
            return $this->redirectWithDbError($e, 'Unable to add the subject right now. Please try again.');
        }

        return redirect()->route('chair.subjects')
            ->with('success', 'Subject added successfully.');
    }

    public function update(Request $request, string $id)
    {
        $subject = Course::findOrFail($id);

        $validated = $request->validate([
            'subj_dept_id' => 'required|exists:college,college_id',
            'subj_code' => [
                'required',
                'string',
                Rule::unique('course', 'course_code')->ignore($subject->course_id, 'course_id'),
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
        $subject = Course::findOrFail($id);

        try {
            $subject->update(['subj_is_active' => false]);
        } catch (\Throwable $e) {
            return $this->redirectWithDbError($e, 'Unable to deactivate the subject right now. Please try again.');
        }

        return redirect()->route('chair.subjects')
            ->with('success', 'Subject deactivated successfully.');
    }
}
