<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Dean;
use App\Models\Departments;
use App\Models\Section;
use App\Models\Section_Subject;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    public function adminIndex()
    {
        $this->requireRole('system_admin');

        return $this->renderIndex(request(), null, 'admin.sections');
    }

    public function deanIndex()
    {
        $collegeId = $this->deanCollegeId();

        return $this->renderIndex(request(), $collegeId, 'dean.sections');
    }

    public function adminStore(Request $request)
    {
        return $this->storeForRole($request, 'system_admin');
    }

    public function deanStore(Request $request)
    {
        return $this->storeForRole($request, 'dean');
    }

    public function adminUpdate(Request $request, string $section)
    {
        return $this->updateForRole($request, $section, 'system_admin');
    }

    public function deanUpdate(Request $request, string $section)
    {
        return $this->updateForRole($request, $section, 'dean');
    }

    public function adminDestroy(string $section)
    {
        return $this->destroyForRole($section, 'system_admin');
    }

    public function deanDestroy(string $section)
    {
        return $this->destroyForRole($section, 'dean');
    }

    private function renderIndex(Request $request, ?string $collegeId, string $routePrefix)
    {
        $filterValue = fn (string $key): string => is_string($request->query($key))
            ? trim($request->query($key))
            : '';
        $yearLevel = $filterValue('year_level');
        $shift = $filterValue('shift');

        $filters = [
            'department' => $filterValue('department'),
            'year_level' => in_array($yearLevel, ['1', '2', '3', '4', '5'], true) ? $yearLevel : '',
            'shift' => in_array($shift, ['day', 'evening'], true) ? $shift : '',
        ];

        $sectionsQuery = Section::query()
            ->with(['program.department', 'academicYear', 'semester'])
            ->when($collegeId, fn ($query) => $query->whereHas(
                'program',
                fn ($programQuery) => $programQuery->where('dept_college_id', $collegeId)
            ))
            ->when($filters['department'], fn ($query, $departmentId) => $query->where('sec_dept_id', $departmentId))
            ->when($filters['year_level'], fn ($query, $yearLevel) => $query->where('sec_year_level', $yearLevel))
            ->when($filters['shift'] !== '', fn ($query) => $query->where(
                'sec_is_evening',
                $filters['shift'] === 'evening'
            ))
            ->orderBy('sec_year_level')
            ->orderBy('sec_name');

        $sections = $sectionsQuery->get();

        $programs = Departments::query()
            ->with('department')
            ->when($collegeId, fn ($query) => $query->where('dept_college_id', $collegeId))
            ->orderBy('dept_name')
            ->get();
        $academicYears = AcademicYear::query()->orderByDesc('ay_academic_year')->get();
        $semesters = Semester::query()->with('academicYear')->orderBy('sem_name')->get();
        $activeAcademicYear = $academicYears->firstWhere('ay_is_active', true);
        $activeSemester = $semesters->firstWhere('sem_is_active', true);

        return view('admin.sections', [
            'sections' => $sections,
            'programs' => $programs,
            'academicYears' => $academicYears,
            'semesters' => $semesters,
            'activeAcademicYear' => $activeAcademicYear,
            'activeSemester' => $activeSemester,
            'routePrefix' => $routePrefix,
            'isDean' => $routePrefix === 'dean.sections',
            'filters' => $filters,
        ]);
    }

    private function storeForRole(Request $request, string $role)
    {
        $collegeId = $this->collegeForRole($role);
        $validated = $this->validateSection($request, $collegeId);

        Section::create([
            ...$validated,
            'sec_no_of_student' => 0,
            'sec_is_evening' => $request->boolean('sec_is_evening'),
            'sec_status' => $validated['sec_status'] ?? 'active',
        ]);

        return redirect()->route($role === 'dean' ? 'dean.sections' : 'admin.sections')
            ->with('success', 'Section created successfully.');
    }

    private function updateForRole(Request $request, string $id, string $role)
    {
        $collegeId = $this->collegeForRole($role);
        $section = $this->sectionQuery($collegeId)->findOrFail($id);
        $validated = $this->validateSection($request, $collegeId, $section);

        $section->update([
            ...$validated,
            'sec_is_evening' => $request->boolean('sec_is_evening'),
        ]);

        return redirect()->route($role === 'dean' ? 'dean.sections' : 'admin.sections')
            ->with('success', 'Section updated successfully.');
    }

    private function destroyForRole(string $id, string $role)
    {
        $collegeId = $this->collegeForRole($role);
        $section = $this->sectionQuery($collegeId)->findOrFail($id);

        if (
            $section->studyLoads()->exists()
            || $section->schedules()->exists()
            || Section_Subject::query()->where('ssub_sec_id', $section->sec_id)->exists()
        ) {
            return back()->with('error', 'This section is already used by scheduling records and cannot be deleted.');
        }

        $section->delete();

        return redirect()->route($role === 'dean' ? 'dean.sections' : 'admin.sections')
            ->with('success', 'Section deleted successfully.');
    }

    private function validateSection(Request $request, ?string $collegeId, ?Section $section = null): array
    {
        $programRule = Rule::exists('department', 'dept_id');
        $uniqueNameRule = Rule::unique('section', 'sec_name')
            ->where(fn ($query) => $query
                ->where('sec_dept_id', $request->input('sec_dept_id'))
                ->where('sec_ay_id', $request->input('sec_ay_id'))
                ->where('sec_sem_id', $request->input('sec_sem_id')));

        if ($collegeId) {
            $programRule->where('dept_college_id', $collegeId);
        }

        if ($section) {
            $uniqueNameRule->ignore($section->sec_id, 'sec_id');
        }

        $studentCount = (int) ($section?->sec_no_of_student ?? 0);

        return $request->validate([
            'sec_dept_id' => ['required', 'uuid', $programRule],
            'sec_ay_id' => ['required', 'uuid', 'exists:academic_year,ay_id'],
            'sec_sem_id' => [
                'required',
                'uuid',
                Rule::exists('semester', 'sem_id')->where('sem_ay_id', $request->input('sec_ay_id')),
            ],
            'sec_name' => ['required', 'string', 'max:50', $uniqueNameRule],
            'sec_year_level' => ['required', 'integer', 'min:1', 'max:5'],
            'sec_max_capacity' => ['required', 'integer', 'min:'.max(1, $studentCount)],
            'sec_status' => ['required', Rule::in(['active', 'inactive'])],
            'sec_is_evening' => ['nullable', 'boolean'],
        ]);
    }

    private function sectionQuery(?string $collegeId)
    {
        return Section::query()->when($collegeId, fn ($query) => $query->whereHas(
            'program',
            fn ($programQuery) => $programQuery->where('dept_college_id', $collegeId)
        ));
    }

    private function collegeForRole(string $role): ?string
    {
        $this->requireRole($role);

        return $role === 'dean' ? $this->deanCollegeId() : null;
    }

    private function deanCollegeId(): string
    {
        $this->requireRole('dean');
        $dean = Dean::query()->where('dean_usr_id', Auth::id())->first();

        abort_unless($dean?->dean_college_id, 403, 'Your account is not assigned to a college.');

        return $dean->dean_college_id;
    }

    private function requireRole(string $role): void
    {
        abort_unless(Auth::user()?->usr_role === $role, 403, 'Unauthorized action.');
    }
}
