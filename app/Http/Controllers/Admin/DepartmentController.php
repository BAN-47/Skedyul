<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Departments;
use App\Models\Audit_Log;
use App\Services\AuditActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $colleges = College::withCount(['programs', 'faculty', 'subjects', 'deptChairs'])
            ->orderBy('college_name')->get();
        $filters = request()->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'college_id' => ['nullable', 'string', 'exists:college,college_id'],
            'designation' => ['nullable', 'string', 'max:150'],
        ]);
        $departmentQuery = Departments::query()
            ->with([
                'department',
                'faculty' => fn ($query) => $query->where('fac_is_placeholder', false)->with('user'),
                'chairs.user',
            ])
            ->withCount([
                'faculty' => fn ($query) => $query->where('fac_is_placeholder', false),
                'subjects',
            ]);
        $likeOperator = DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
        if ($filters['college_id'] ?? null) {
            $departmentQuery->where('dept_college_id', $filters['college_id']);
        }
        $selectedCollege = ($filters['college_id'] ?? null)
            ? $colleges->firstWhere('college_id', $filters['college_id'])
            : null;
        $selectedDepartment = $selectedCollege && $request->filled('department_id')
            ? Departments::where('dept_college_id', $selectedCollege->college_id)
                ->with(['department', 'chairs.user'])
                ->withCount(['faculty' => fn ($query) => $query->where('fac_is_placeholder', false)])
                ->find($request->input('department_id'))
            : null;
        $departmentFaculty = $selectedDepartment
            ? $selectedDepartment->faculty()
                ->where('fac_is_placeholder', false)
                ->with('user')
                ->orderBy('fac_last_name')
                ->orderBy('fac_first_name')
                ->paginate(15, ['*'], 'faculty_page')
                ->withQueryString()
            : null;
        $editingCollege = request()->filled('edit_college')
            ? $colleges->firstWhere('college_id', request('edit_college'))
            : null;
        if ($filters['search'] ?? null) {
            $term = '%' . addcslashes($filters['search'], '%_\\') . '%';
            $departmentQuery->where(fn ($query) => $query->where('dept_name', $likeOperator, $term)
                ->orWhere('dept_code', $likeOperator, $term)
                ->orWhereHas('department', fn ($collegeQuery) => $collegeQuery->where('college_name', $likeOperator, $term)->orWhere('college_code', $likeOperator, $term)));
        }
        if ($filters['designation'] ?? null) {
            $departmentQuery->whereHas('faculty', fn ($facultyQuery) => $facultyQuery->where('fac_special_position', $filters['designation']));
        }
        $departmentRows = $departmentQuery->orderBy('dept_name')->paginate(12, ['*'], 'department_page')->withQueryString();
        $editingDepartment = ($selectedCollege && request()->filled('edit_department'))
            ? Departments::where('dept_college_id', $selectedCollege->college_id)->find(request('edit_department'))
            : null;
        $designations = DB::table('faculty')->whereNotNull('fac_special_position')->where('fac_special_position', '<>', '')
            ->distinct()->orderBy('fac_special_position')->pluck('fac_special_position');
        $departmentHistory = Audit_Log::with('user')
            ->where(function ($query): void {
                $query->whereIn('al_target_table', ['department', 'faculty', 'department_chair', 'dean', 'USER'])
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->whereIn('usr_role', ['dean', 'department_chair', 'faculty']));
            })
            ->orderByDesc('al_created_at')->paginate(10, ['*'], 'history_page')->withQueryString();

        return view('admin.departments', compact('colleges', 'selectedCollege', 'selectedDepartment', 'departmentFaculty', 'editingCollege', 'editingDepartment', 'departmentRows', 'designations', 'departmentHistory', 'filters'));
    }

    public function storeDepartment(Request $request)
    {
        $data = $request->validate([
            'dept_college_id' => ['required', 'string', 'not_regex:/[<>]/', 'exists:college,college_id', 'regex:/^[\p{L}\p{N}-]+$/u'],
            'dept_name' => ['required', 'string', 'not_regex:/[<>]/', 'max:255', 'regex:/^[\p{L}\p{N}-]+$/u'],
            'dept_code' => ['required', 'string', 'not_regex:/[<>]/', 'max:50', Rule::unique('department', 'dept_code')->where('dept_college_id', $request->input('dept_college_id')), 'regex:/^[\p{L}\p{N}-]+$/u'],
        ], [
            'not_regex' => 'Angle brackets (< and >) are not allowed. Use letters, numbers, and hyphens only.',
        ]);
        $department = Departments::create($data);
        AuditActivityLogger::record($request->user(), 'Created department', 'department', 'Created ' . $department->dept_code . ' in ' . College::find($department->dept_college_id)?->college_code . '.', $request->ip(), $department->dept_id);
        return back()->with('success', 'Department added.');
    }

    public function updateDepartment(Request $request, string $id)
    {
        $department = Departments::findOrFail($id);
        $data = $request->validate([
            'dept_college_id' => ['required', 'string', 'not_regex:/[<>]/', 'exists:college,college_id', 'regex:/^[\p{L}\p{N}-]+$/u'],
            'dept_name' => ['required', 'string', 'not_regex:/[<>]/', 'max:255', 'regex:/^[\p{L}\p{N}-]+$/u'],
            'dept_code' => ['required', 'string', 'not_regex:/[<>]/', 'max:50', Rule::unique('department', 'dept_code')->where('dept_college_id', $request->input('dept_college_id'))->ignore($department->dept_id, 'dept_id'), 'regex:/^[\p{L}\p{N}-]+$/u'],
        ], [
            'not_regex' => 'Angle brackets (< and >) are not allowed. Use letters, numbers, and hyphens only.',
        ]);

        $before = $department->only(['dept_name', 'dept_code', 'dept_college_id']);
        $department->update($data);
        AuditActivityLogger::record($request->user(), 'Updated department', 'department', 'Updated ' . $before['dept_code'] . ' to ' . $department->dept_code . '.', $request->ip(), $department->dept_id);
        return back()->with('success', 'Department updated.');
    }

    public function destroyDepartment(string $id)
    {
        $department = Departments::findOrFail($id);
        foreach ([
            'faculty' => 'fac_dept_id', 'course' => 'course_dept_id', 'section' => 'sec_dept_id',
            'department_chair' => 'dc_dept_id', 'schedule_submission' => 'schsub_dept_id',
            'dean' => 'dean_dept_id',
        ] as $table => $column) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)
                && DB::table($table)->where($column, $department->dept_id)->exists()) {
                return back()->with('error', 'This department is still linked to faculty, courses, sections, a Chair, or submissions. Move those records first.');
            }
        }
        $label = $department->dept_code;
        $department->delete();
        AuditActivityLogger::record(request()->user(), 'Deleted department', 'department', 'Deleted department ' . $label . '.', request()->ip(), $id);
        return back()->with('success', 'Department permanently deleted.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'college_name' => ['required', 'string', 'not_regex:/[<>]/', 'max:255', 'regex:/^[\p{L}\p{N}-]+$/u', Rule::unique('college', 'college_name')],
            'college_code' => ['required', 'string', 'not_regex:/[<>]/', 'max:50', 'regex:/^[\p{L}\p{N}-]+$/u', Rule::unique('college', 'college_code')],
        ], [
            'not_regex' => 'Angle brackets (< and >) are not allowed. Use letters, numbers, and hyphens only.',
        ]);

        College::create($data);

        return back()->with('success', 'College added.');
    }

    public function update(Request $request, string $id)
    {
        $college = College::findOrFail($id);
        $data = $request->validate([
            'college_name' => ['required', 'string', 'not_regex:/[<>]/', 'max:255', 'regex:/^[\p{L}\p{N}-]+$/u', Rule::unique('college', 'college_name')->ignore($college->college_id, 'college_id')],
            'college_code' => ['required', 'string', 'not_regex:/[<>]/', 'max:50', 'regex:/^[\p{L}\p{N}-]+$/u', Rule::unique('college', 'college_code')->ignore($college->college_id, 'college_id')],
        ], [
            'not_regex' => 'Angle brackets (< and >) are not allowed. Use letters, numbers, and hyphens only.',
        ]);

        $college->update($data);

        return back()->with('success', 'College updated.');
    }

    public function destroy(string $id)
    {
        $college = College::findOrFail($id);
        $dependencies = [
            'department' => 'dept_college_id', 'faculty' => 'fac_college_id',
            'room' => 'room_college_id', 'course' => 'course_college_id',
            'department_chair' => 'dc_college_id', 'dean' => 'dean_college_id',
        ];
        foreach ($dependencies as $table => $column) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)
                && DB::table($table)->where($column, $college->college_id)->exists()) {
                return back()->with('error', 'This college still has departments, users, rooms, or courses. Move or remove those records first.');
            }
        }

        $college->delete();

        return back()->with('success', 'College permanently deleted.');
    }
}
