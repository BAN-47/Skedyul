<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Departments;
use App\Models\Faculty;
use App\Models\Section;
use App\Models\Study_Load;
use App\Models\Dept_Chair;
use App\Models\Dean;
use App\Models\Schedule_Submission;
use App\Models\Schedule;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\User;
use App\Models\Audit_Log;
use App\Services\AuditActivityLogger;
use App\Models\Designation;
use App\Services\ScheduleAssignmentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\LengthAwarePaginator;

class DeanDepartmentController extends Controller
{
    private function assignedCollege(): College
    {
        $dean = Dean::where('dean_usr_id', Auth::id())->firstOrFail();
        abort_unless($dean->dean_college_id, 403, 'Your account is not assigned to a college.');

        return College::findOrFail($dean->dean_college_id);
    }

    public function storeDepartment(Request $request)
    {
        $college = $this->assignedCollege();
        $data = $request->validate([
            'dept_name' => ['required', 'string', 'max:255'],
            'dept_code' => ['required', 'string', 'max:50', Rule::unique('department', 'dept_code')->where('dept_college_id', $college->college_id)],
        ]);
        $college->programs()->create($data);

        return back()->with('success', 'Department added to ' . $college->college_name . '.');
    }

    public function updateDepartment(Request $request, string $id)
    {
        $college = $this->assignedCollege();
        $department = Departments::where('dept_college_id', $college->college_id)->findOrFail($id);
        $data = $request->validate([
            'dept_name' => ['required', 'string', 'max:255'],
            'dept_code' => ['required', 'string', 'max:50', Rule::unique('department', 'dept_code')->where('dept_college_id', $college->college_id)->ignore($department->dept_id, 'dept_id')],
        ]);
        $department->update($data);

        return back()->with('success', 'Department updated.');
    }

    public function destroyDepartment(string $id)
    {
        $college = $this->assignedCollege();
        $department = Departments::where('dept_college_id', $college->college_id)->findOrFail($id);
        foreach ([
            'faculty' => 'fac_dept_id', 'course' => 'course_dept_id', 'section' => 'sec_dept_id',
            'department_chair' => 'dc_dept_id', 'schedule_submission' => 'schsub_dept_id',
            'dean' => 'dean_dept_id',
        ] as $table => $column) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)
                && DB::table($table)->where($column, $department->dept_id)->exists()) {
                return back()->with('error', 'This department is still linked to faculty, courses, sections, a Chair, or submissions. Remove or move those records first.');
            }
        }

        $department->delete();

        return back()->with('success', 'Department permanently deleted.');
    }

    /** Assign a faculty member as this department's chair, keeping the former assignment in history. */
    public function assignChair(Request $request, string $id)
    {
        $college = $this->assignedCollege();
        $department = Departments::where('dept_college_id', $college->college_id)->findOrFail($id);
        $data = $request->validate([
            'faculty_id' => ['required', 'string', 'exists:faculty,fac_id'],
            'confirm_replace' => ['nullable', 'accepted'],
        ]);

        $faculty = Faculty::where('fac_college_id', $college->college_id)
            ->where('fac_dept_id', $department->dept_id)
            ->where('fac_is_placeholder', false)
            ->with('user')
            ->findOrFail($data['faculty_id']);
        $user = $faculty->user;
        abort_unless($user && $user->usr_is_active, 422, 'This faculty member does not have an active account.');
        abort_unless(in_array($user->usr_role, ['faculty', 'department_chair'], true), 422, 'Only an active faculty account can be assigned as department chair.');

        $current = Dept_Chair::where('dc_dept_id', $department->dept_id)->first();
        if ($current?->dc_usr_id === $user->usr_id) {
            return back()->with('success', 'This faculty member is already the department chair.');
        }
        if ($current && !$request->boolean('confirm_replace')) {
            return back()->withErrors(['faculty_id' => 'A chair is already assigned. Confirm replacement to continue.']);
        }
        $otherChair = Dept_Chair::where('dc_usr_id', $user->usr_id)
            ->where('dc_dept_id', '!=', $department->dept_id)
            ->exists();
        if ($otherChair) {
            return back()->withErrors(['faculty_id' => 'This person is already assigned as chair of another department. Unassign them there first.']);
        }

        DB::transaction(function () use ($department, $faculty, $user, $current, $request): void {
            if ($current) {
                $formerUser = User::find($current->dc_usr_id);
                $this->writeChairHistory($department->dept_id, $current->dc_usr_id, $user->usr_id, 'replaced', $formerUser?->usr_name, $user->usr_name, $request->user()->usr_name);
                Dept_Chair::where('dc_id', $current->dc_id)->delete();
                if ($formerUser && $formerUser->usr_role === 'department_chair') {
                    $formerUser->update(['usr_role' => 'faculty']);
                }
                if ($formerUser) {
                    // Their chair designation ends with the chair assignment.
                    Faculty::where('fac_usr_id', $formerUser->usr_id)
                        ->update(['fac_special_position' => null, 'fac_special_position_max_hours' => null]);
                }
            } else {
                $this->writeChairHistory($department->dept_id, null, $user->usr_id, 'assigned', null, $user->usr_name, $request->user()->usr_name);
            }

            $chair = new Dept_Chair();
            $chair->forceFill([
                'dc_usr_id' => $user->usr_id,
                'dc_college_id' => $department->dept_college_id,
                'dc_dept_id' => $department->dept_id,
                'dc_first_name' => $faculty->fac_first_name,
                'dc_middle_name' => $faculty->fac_middle_name,
                'dc_last_name' => $faculty->fac_last_name,
                'dc_suffix' => $faculty->fac_suffix,
                'dc_employee_id' => $faculty->fac_employee_id,
                'dc_gender' => $faculty->fac_gender,
                'dc_civil_status' => $faculty->fac_civil_status,
                'dc_dob' => $faculty->fac_dob,
                'dc_nationality' => $faculty->fac_nationality,
                'dc_phone_number' => $faculty->fac_phone_number,
                'dc_address' => $faculty->fac_address,
                'dc_bio' => $faculty->fac_bio,
            ]);
            if (Schema::hasColumn('department_chair', 'dc_assigned_at')) {
                $chair->setAttribute('dc_assigned_at', now());
            }
            $chair->save();
            $user->update(['usr_role' => 'department_chair']);
            // Becoming chair replaces any previous special position and applies the chair's 16-hour cap.
            $designation = Designation::where('name', 'Department Chairperson')->first();
            Faculty::where('fac_id', $faculty->fac_id)->update([
                'fac_special_position' => 'Department Chairperson',
                'fac_special_position_max_hours' => $designation?->day_cap ?? 16,
            ]);
        });

        $formerName = $current ? User::find($current->dc_usr_id)?->usr_name : null;
        AuditActivityLogger::record($request->user(), $current ? 'Replaced department chair' : 'Assigned department chair', 'department_chair', ($current ? 'Replaced ' . ($formerName ?? 'the former chair') . ' with ' : 'Assigned ') . $user->usr_name . ' as chair for ' . $department->dept_code . '.', $request->ip(), $department->dept_id);
        return back()->with('success', $current ? 'Department chair replaced. The former chair is now a faculty account.' : 'Department chair assigned.');
    }

    /** Remove the chair role while preserving the faculty account and chair history. */
    public function unassignChair(Request $request, string $id)
    {
        $college = $this->assignedCollege();
        $department = Departments::where('dept_college_id', $college->college_id)->findOrFail($id);
        $chair = Dept_Chair::where('dc_dept_id', $department->dept_id)->firstOrFail();
        $formerUser = User::find($chair->dc_usr_id);

        DB::transaction(function () use ($chair, $department, $formerUser, $request): void {
            $this->writeChairHistory($department->dept_id, $chair->dc_usr_id, null, 'unassigned', $formerUser?->usr_name, null, $request->user()->usr_name);
            Dept_Chair::where('dc_id', $chair->dc_id)->delete();
            if ($formerUser && $formerUser->usr_role === 'department_chair') {
                $formerUser->update(['usr_role' => 'faculty']);
            }
            if ($formerUser) {
                Faculty::where('fac_usr_id', $formerUser->usr_id)
                    ->update(['fac_special_position' => null, 'fac_special_position_max_hours' => null]);
            }
        });

        AuditActivityLogger::record($request->user(), 'Unassigned department chair', 'department_chair', 'Unassigned ' . ($formerUser?->usr_name ?? 'the chair') . ' from ' . $department->dept_code . '.', $request->ip(), $department->dept_id);
        return back()->with('success', 'Department chair unassigned. Their faculty account and schedules were kept.');
    }

    /** Move an existing faculty member's home department inside the Dean's college. */
    public function moveFaculty(Request $request, string $id)
    {
        $college = $this->assignedCollege();
        $department = Departments::where('dept_college_id', $college->college_id)->findOrFail($id);
        $data = $request->validate(['faculty_id' => ['required', 'string', 'exists:faculty,fac_id']]);
        $faculty = Faculty::where('fac_college_id', $college->college_id)
            ->where('fac_is_placeholder', false)->with('user')->findOrFail($data['faculty_id']);
        if (!$faculty->user || $faculty->user->usr_role !== 'faculty') {
            return back()->withErrors(['faculty_id' => 'Only a regular faculty account can be moved. Unassign its chair role first.']);
        }

        $oldDepartment = Departments::find($faculty->fac_dept_id);
        if ($faculty->fac_dept_id === $department->dept_id) {
            return back()->with('success', 'This faculty member is already assigned to this department.');
        }
        $faculty->update(['fac_dept_id' => $department->dept_id]);
        AuditActivityLogger::record($request->user(), 'Moved faculty to department', 'faculty', 'Moved ' . $faculty->full_name . ' from ' . ($oldDepartment?->dept_code ?? 'Unassigned') . ' to ' . $department->dept_code . '.', $request->ip(), $department->dept_id);

        return redirect()->route('dean.departments', ['department_id' => $department->dept_id])->with('success', $faculty->full_name . ' assigned to ' . $department->dept_code . '. Their existing schedule assignments were kept.');
    }

    /** Create a faculty login already assigned to the department being viewed. */
    public function storeFaculty(Request $request, string $id)
    {
        $college = $this->assignedCollege();
        $department = Departments::where('dept_college_id', $college->college_id)->findOrFail($id);
        $data = $request->validate([
            'usr_first_name' => ['required', 'string', 'max:150'],
            'usr_middle_name' => ['nullable', 'string', 'max:150'],
            'usr_last_name' => ['required', 'string', 'max:150'],
            'usr_suffix' => ['nullable', 'string', 'max:20'],
            'usr_email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'usr_employee_id' => ['nullable', 'string', 'max:255'],
            'usr_rank_title' => ['nullable', 'string', 'max:255'],
            'role_phone_number' => ['required', 'string', 'max:20', 'regex:/^\\+[1-9]\\d{1,3}\\d{7,10}$/'],
            'role_address' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['required', Rule::in(['full_time', 'part_time'])],
            'special_position' => ['nullable', Rule::in(Designation::pluck('name')->toArray())],
            'usr_gender' => ['nullable', 'string', 'max:255'],
            'usr_civil_status' => ['nullable', 'string', 'max:255'],
            'usr_dob' => ['nullable', 'date'],
            'usr_nationality' => ['nullable', 'string', 'max:255'],
        ]);
        $email = strtolower(trim($data['usr_email']));
        if (User::whereRaw('LOWER(usr_email) = ?', [$email])->exists()) {
            return back()->withErrors(['usr_email' => 'That email already has an account.'])->withInput();
        }
        $data['usr_middle_name'] = \App\Services\MiddleInitialFormatter::format($data['usr_middle_name'] ?? null);

        $user = DB::transaction(function () use ($data, $email, $college, $department): User {
            $user = User::create([
                'usr_id' => (string) \Illuminate\Support\Str::uuid(),
                'usr_name' => trim(implode(' ', array_filter([$data['usr_first_name'], $data['usr_middle_name'] ?? null, $data['usr_last_name'], $data['usr_suffix'] ?? null]))),
                'usr_first_name' => $data['usr_first_name'],
                'usr_middle_name' => $data['usr_middle_name'] ?? null,
                'usr_last_name' => $data['usr_last_name'],
                'usr_suffix' => $data['usr_suffix'] ?? null,
                'usr_email' => $email,
                'usr_password_hash' => Hash::make($data['password']),
                'usr_role' => 'faculty',
                'usr_is_active' => true,
            ]);
            Faculty::create([
                'fac_usr_id' => $user->usr_id,
                'fac_college_id' => $college->college_id,
                'fac_dept_id' => $department->dept_id,
                'fac_first_name' => $data['usr_first_name'],
                'fac_middle_name' => $data['usr_middle_name'] ?? null,
                'fac_last_name' => $data['usr_last_name'],
                'fac_suffix' => $data['usr_suffix'] ?? null,
                'fac_employee_id' => $data['usr_employee_id'] ?? null,
                'fac_gender' => $data['usr_gender'] ?? null,
                'fac_civil_status' => $data['usr_civil_status'] ?? null,
                'fac_dob' => $data['usr_dob'] ?? null,
                'fac_nationality' => $data['usr_nationality'] ?? null,
                'fac_phone_number' => $data['role_phone_number'],
                'fac_address' => $data['role_address'] ?? null,
                'fac_employment_type' => $data['employment_type'],
                'fac_rank' => $data['usr_rank_title'] ?? null,
                'fac_special_position' => $data['special_position'] ?? null,
                'fac_special_position_max_hours' => $data['special_position'] 
                    ? Designation::where('name', $data['special_position'])->value('day_cap') 
                    : null,
            ]);
            return $user;
        });

        AuditActivityLogger::record($request->user(), 'Added faculty to department', 'faculty', 'Added faculty account ' . $user->usr_name . ' to ' . $department->dept_code . '.', $request->ip(), $department->dept_id);
        return redirect()->route('dean.departments', ['department_id' => $department->dept_id])->with('success', $user->usr_name . ' was added to ' . $department->dept_code . '.');
    }

    /** Update profile details for a faculty member whose home department is managed by this Dean. */
    public function updateFaculty(Request $request, string $id, string $facultyId)
    {
        $college = $this->assignedCollege();
        $department = Departments::where('dept_college_id', $college->college_id)->findOrFail($id);
        $faculty = Faculty::where('fac_college_id', $college->college_id)
            ->where('fac_dept_id', $department->dept_id)
            ->where('fac_is_placeholder', false)
            ->with('user')
            ->findOrFail($facultyId);
        abort_unless($faculty->user, 422, 'This faculty profile has no linked user account.');

        $data = $request->validate([
            'usr_first_name' => ['required', 'string', 'max:150'],
            'usr_middle_name' => ['nullable', 'string', 'max:150'],
            'usr_last_name' => ['required', 'string', 'max:150'],
            'usr_suffix' => ['nullable', 'string', 'max:20'],
            'usr_email' => ['required', 'email', 'max:255'],
            'fac_employee_id' => ['nullable', 'string', 'max:255', Rule::unique('faculty', 'fac_employee_id')->ignore($faculty->fac_id, 'fac_id')],
            'fac_rank' => ['nullable', 'string', 'max:255'],
            'fac_employment_type' => ['required', Rule::in(['full_time', 'part_time'])],
            'fac_special_position' => ['nullable', Rule::in(Designation::pluck('name')->toArray())],
            'fac_phone_number' => ['nullable', 'string', 'max:20', 'regex:/^\\+[1-9]\\d{1,3}\\d{7,10}$/'],
            'fac_address' => ['nullable', 'string', 'max:255'],
            'dept_id' => ['required', 'string', Rule::exists('department', 'dept_id')->where('dept_college_id', $college->college_id)],
            'usr_is_active' => ['required', 'boolean'],
        ]);

        $email = strtolower(trim($data['usr_email']));
        if (User::whereRaw('LOWER(usr_email) = ?', [$email])->where('usr_id', '!=', $faculty->user->usr_id)->exists()) {
            return back()->withErrors(['usr_email' => 'That email already belongs to another account.'])->withInput();
        }

        $data['usr_middle_name'] = \App\Services\MiddleInitialFormatter::format($data['usr_middle_name'] ?? null);
        DB::transaction(function () use ($faculty, $data, $email): void {
            $faculty->update([
                'fac_first_name' => $data['usr_first_name'],
                'fac_middle_name' => $data['usr_middle_name'],
                'fac_last_name' => $data['usr_last_name'],
                'fac_suffix' => $data['usr_suffix'] ?? null,
                'fac_employee_id' => $data['fac_employee_id'] ?? null,
                'fac_rank' => $data['fac_rank'] ?? null,
                'fac_employment_type' => $data['fac_employment_type'],
                'fac_special_position' => $data['fac_special_position'] ?? null,
                'fac_special_position_max_hours' => $data['fac_special_position'] 
                    ? Designation::where('name', $data['fac_special_position'])->value('day_cap') 
                    : null,
                'fac_phone_number' => $data['fac_phone_number'] ?? null,
                'fac_address' => $data['fac_address'] ?? null,
                'fac_dept_id' => $data['dept_id'],
            ]);

            // Keep the linked login name, email, and account status in sync with the edit form.
            $faculty->user->update([
                'usr_name' => trim(implode(' ', array_filter([
                    $data['usr_first_name'], $data['usr_middle_name'] ?? null,
                    $data['usr_last_name'], $data['usr_suffix'] ?? null,
                ]))),
                'usr_first_name' => $data['usr_first_name'],
                'usr_middle_name' => $data['usr_middle_name'] ?? null,
                'usr_last_name' => $data['usr_last_name'],
                'usr_suffix' => $data['usr_suffix'] ?? null,
                'usr_email' => $email,
                'usr_is_active' => (bool) $data['usr_is_active'],
            ]);
        });

        AuditActivityLogger::record($request->user(), 'Updated faculty profile', 'faculty', 'Updated profile details for ' . $faculty->full_name . ' in ' . $department->dept_code . '.', $request->ip(), $faculty->fac_id);

        return redirect()->route('dean.departments', ['department_id' => $department->dept_id])->with('success', $faculty->full_name . '’s faculty details were updated.');
    }

    /** Keep a readable snapshot so history survives later profile edits. */
    private function writeChairHistory(string $departmentId, ?string $formerId, ?string $newId, string $action, ?string $formerName, ?string $newName, string $changedBy): void
    {
        if (!Schema::hasTable('past_position_history')) {
            return; // Audit_Log still records the change, so missing optional storage will not block it.
        }
        DB::table('past_position_history')->insert([
            'pph_id' => (string) \Illuminate\Support\Str::uuid(),
            'pph_position' => 'department_chair',
            'pph_scope_type' => 'department',
            'pph_scope_id' => $departmentId,
            'pph_former_usr_id' => $formerId,
            'pph_new_usr_id' => $newId,
            'pph_former_name' => $formerName,
            'pph_new_name' => $newName,
            'pph_action' => $action,
            'pph_changed_by' => $changedBy,
            'pph_created_at' => now(),
        ]);
    }

    // Keep these in sync with DeanFacultyWorkloadController / DeanDashboardController
    const FULL_TIME_MAX_HOURS = 30;
    const PART_TIME_MAX_HOURS = 30;
    const NEAR_MAX_BUFFER     = 3;
    const OK_THRESHOLD       = 20;

    // Cycles through brand colors per department card, in department order.
    const DEPT_COLORS = ['blue', 'purple', 'teal', 'amber', 'red', 'green'];

    public function index()
    {
        $academicYear = AcademicYear::where('ay_is_active', true)->first();
        $semester     = Semester::where('sem_is_active', true)->first();

        // Each Dean is assigned to exactly one Department via department_dean.
        // Only show that department here — not the whole university.
        $deanAssignment = Dean::where('dean_usr_id', Auth::id())->first();
        $assignedCollege = $deanAssignment?->department;
        $managedDepartments = $assignedCollege
            ? Departments::where('dept_college_id', $assignedCollege->college_id)->orderBy('dept_name')->get()
            : collect();

        $departments = $deanAssignment
            ? College::where('college_id', $deanAssignment->dean_college_id)->get()
            : collect(); // Dean has no department assigned yet — show nothing rather than everything

        // Calculate from course assignments so legacy workload rows that
        // stored academic units are not shown as teaching hours.
        $loadsByFaculty = Study_Load::with('subject')
            ->when($semester, fn($q) => $q->where('sl_sem_id', $semester->sem_id))
            ->get()
            ->groupBy('sl_fac_id');
        $hoursByFaculty = $loadsByFaculty->map(fn ($loads) => $loads->sum(
            fn ($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject)
        ));

        $deptData = [];

        foreach ($departments as $index => $dept) {
            $facultyList = Faculty::where('fac_is_placeholder', false)
                ->where('fac_college_id', $dept->college_id)->get();

            $facultyCount = $facultyList->count();

            // Only faculty with a course assignment count toward the average.
            $assignedFaculty = $facultyList->filter(fn($f) => $loadsByFaculty->has($f->fac_id));
            $avgLoad = $assignedFaculty->count() > 0
                ? round($assignedFaculty->sum(fn($f) => $hoursByFaculty->get($f->fac_id, 0)) / $assignedFaculty->count())
                : 0;

            $loadPct = min(100, round(($avgLoad / self::FULL_TIME_MAX_HOURS) * 100));

            // ---------- PROGRAM BREAKDOWN ----------
            // A faculty member "belongs" to whichever program(s) they're
            // currently assigned subjects for this semester (via Study_Load),
            // since Faculty itself is only tagged at the Department level.
            $programs = Departments::where('dept_college_id', $dept->college_id)->orderBy('dept_code')->get();

            $programsData = [];

            foreach ($programs as $progIndex => $prog) {
                $facultyIds = Study_Load::whereHas('subject', fn($q) => $q->where('course_dept_id', $prog->dept_id))
                    ->when($semester, fn($q) => $q->where('sl_sem_id', $semester->sem_id))
                    ->pluck('sl_fac_id')
                    ->unique();

                // Faculty statically assigned to this program (fac_prog_id), even
                // with no study_load record yet this semester
                $staticFacultyIds = Faculty::where('fac_is_placeholder', false)
                    ->where('fac_dept_id', $prog->dept_id)->pluck('fac_id');

                $facultyIds = $facultyIds->merge($staticFacultyIds)->unique();

                $progFaculty = Faculty::where('fac_is_placeholder', false)->whereIn('fac_id', $facultyIds)->get();

                $progFacultyRows = $progFaculty->map(function ($f) use ($loadsByFaculty, $hoursByFaculty) {
                    $hasWorkload = $loadsByFaculty->has($f->fac_id);
                    $hours = $hasWorkload ? $hoursByFaculty->get($f->fac_id, 0) : 0;
                    [$statusLabel, $statusColor] = $this->resolveStatus($hours, $f->fac_employment_type, $hasWorkload);

                    return [
                        'name'       => $f->full_name ?: 'Unnamed Faculty',
                        'rank'       => $f->fac_rank ?? '—',
                        'employment' => $this->formatEmployment($f->fac_employment_type),
                        'load'       => $hasWorkload ? $hours . 'h' : '—',
                        'status'     => $statusLabel,
                        'badge'      => 'badge-' . $statusColor,
                    ];
                })->values();

                $progFacultyCount = $progFaculty->count();

                $progAssignedFaculty = $progFaculty->filter(fn($f) => $loadsByFaculty->has($f->fac_id));
                $progAvgLoad = $progAssignedFaculty->count() > 0
                    ? round($progAssignedFaculty->sum(fn($f) => $hoursByFaculty->get($f->fac_id, 0)) / $progAssignedFaculty->count())
                    : 0;
                $progLoadPct = min(100, round(($progAvgLoad / self::FULL_TIME_MAX_HOURS) * 100));

                // Section ownership is tied to the department; count all years and semesters for the overview.
                $progSectionCount = Section::where('sec_dept_id', $prog->dept_id)->count();
                $activeFacultyCount = Faculty::whereIn('fac_id', $facultyIds)->whereHas('user', fn ($query) => $query->where('usr_is_active', true))->count();
                $plottedCourseCount = Study_Load::whereHas('subject', fn ($query) => $query->where('course_dept_id', $prog->dept_id))
                    ->when($semester, fn ($query) => $query->where('sl_sem_id', $semester->sem_id))
                    ->distinct('sl_course_id')->count('sl_course_id');

                $programsData[$prog->prog_id] = [
                    'code'         => $prog->prog_code,
                    'name'         => $prog->prog_name,
                    'color'        => 'var(--' . self::DEPT_COLORS[$progIndex % count(self::DEPT_COLORS)] . ')',
                    'chair'        => $this->resolveProgramChairName($prog->prog_id),
                    'facultyCount' => $progFacultyCount,
                    'sections'     => $progSectionCount,
                    'activeFaculty' => $activeFacultyCount,
                    'plottedCourses' => $plottedCourseCount,
                    'loadPct'      => $progLoadPct,
                    'loadColor'    => $this->resolveLoadColor($progAvgLoad),
                    'faculty'      => $progFacultyRows,
                ];
            }

            $sectionCount = Section::whereHas('program', fn($q) => $q->where('dept_college_id', $dept->college_id))
                ->when($semester, fn($q) => $q->where('sec_sem_id', $semester->sem_id))
                ->when($academicYear, fn($q) => $q->where('sec_ay_id', $academicYear->ay_id))
                ->count();

            $submission = Schedule_Submission::where('schsub_dept_id', $dept->college_id)
                ->when($semester, fn($q) => $q->where('schsub_sem_id', $semester->sem_id))
                ->orderByDesc('schsub_submitted_at')
                ->first();

            [$scheduleStatus, $statusBadge] = $this->resolveScheduleStatus($submission);

            $deptData[$dept->dept_id] = [
                'code'           => $dept->dept_code,
                'color'          => 'var(--' . self::DEPT_COLORS[$index % count(self::DEPT_COLORS)] . ')',
                'name'           => $dept->dept_name,
                'facultyCount'   => $facultyCount,
                'sections'       => $sectionCount,
                'avgLoad'        => $assignedFaculty->count() > 0 ? $avgLoad . 'h' : '—',
                'maxLoad'        => 'FT ' . self::FULL_TIME_MAX_HOURS . 'h / PT ' . self::PART_TIME_MAX_HOURS . 'h',
                'loadPct'        => $loadPct,
                'loadColor'      => $this->resolveLoadColor($avgLoad),
                'scheduleStatus' => $scheduleStatus,
                'statusBadge'    => $statusBadge,
                'programs'       => $programsData,
            ];
        }

        $selectedDepartment = request()->filled('department_id')
            ? $managedDepartments->firstWhere('dept_id', request('department_id'))
            : null;
        $facultyMembers = collect();
        $currentChair = null;
        $chairHistory = collect();
        $departmentActivity = collect();
        $deanHistory = collect();
        $currentDean = null;
        $collegeFaculty = collect();
        $pendingReviewCount = 0;
        $workloadAttentionCount = 0;
        $chairHistoryAvailable = Schema::hasTable('past_position_history');
        if ($selectedDepartment) {
            // Show all sections owned by this department, including archived terms.
            $sectionQuery = Section::where('sec_dept_id', $selectedDepartment->dept_id);
            $sectionCount = (clone $sectionQuery)->count();
            $sectionIds = $sectionQuery->pluck('sec_id');
            // This roster is for the selected department's own faculty only.
            $homeFacultyIds = Faculty::where('fac_dept_id', $selectedDepartment->dept_id)->pluck('fac_id');
            $facultyIds = $homeFacultyIds->unique()->values();

            $facultyMembers = Faculty::whereIn('fac_id', $facultyIds)
                ->where('fac_is_placeholder', false)
                ->with(['user', 'program'])
                ->orderBy('fac_last_name')->orderBy('fac_first_name')->get()
                ->map(function (Faculty $member) use ($semester) {
                    $loads = Study_Load::with(['subject', 'section', 'schedules'])->where('sl_fac_id', $member->fac_id)
                        ->when($semester, fn ($query) => $query->where('sl_sem_id', $semester->sem_id))->get();
                    $shiftForLoad = fn ($load) => $load->section?->sec_is_evening
                        ? 'evening'
                        : strtolower((string) ($load->sl_shift ?: 'day'));
                    $dayLoads = $loads->filter(fn ($load) => $shiftForLoad($load) !== 'evening');
                    $eveningLoads = $loads->filter(fn ($load) => $shiftForLoad($load) === 'evening');
                    $member->day_load_hours = ScheduleAssignmentService::scheduledHoursForLoads($dayLoads);
                    $member->evening_load_hours = ScheduleAssignmentService::scheduledHoursForLoads($eveningLoads);
                    $member->current_load_hours = $member->day_load_hours + $member->evening_load_hours;
                    $member->day_load_cap = ScheduleAssignmentService::facultyMaxHours($member, 'day');
                    $member->evening_load_cap = ScheduleAssignmentService::facultyMaxHours($member, 'evening');
                    [$member->load_status, $member->load_badge] = $this->resolveStatus($member->current_load_hours, $member->fac_employment_type, $loads->isNotEmpty());
                    return $member;
                });
            $facultyTotal = $facultyMembers->count();
            $activeFacultyCount = $facultyMembers->filter(fn ($member) => (bool) $member->user?->usr_is_active)->count();
            $dayLoadTotal = $facultyMembers->sum('day_load_hours');
            $eveningLoadTotal = $facultyMembers->sum('evening_load_hours');
            $workloadAttentionCount = $facultyMembers->filter(fn ($member) => in_array($member->load_status, ['Near Max', 'Overload'], true))->count();
            $pendingReviewCount = $semester
                ? Schedule_Submission::where('schsub_dept_id', $selectedDepartment->dept_id)
                    ->where('schsub_sem_id', $semester->sem_id)
                    ->where('schsub_status', 'pending')
                    ->whereIn('schsub_type', ['pbs', 'pbt'])
                    ->count()
                : 0;
            $facultyPage = max(1, LengthAwarePaginator::resolveCurrentPage('faculty_page'));
            $facultyMembers = new LengthAwarePaginator(
                $facultyMembers->forPage($facultyPage, 10)->values(),
                $facultyTotal,
                10,
                $facultyPage,
                ['path' => LengthAwarePaginator::resolveCurrentPath(), 'pageName' => 'faculty_page', 'query' => request()->query()]
            );
            $currentChair = Dept_Chair::with('user')->where('dc_dept_id', $selectedDepartment->dept_id)->first();
            $currentDean = Dean::with('user')->where('dean_college_id', $assignedCollege->college_id)->first();
            $deanHistory = Audit_Log::with('user')
                ->where('al_target_table', 'dean')
                ->whereIn('al_target_id', [$assignedCollege->college_id, $selectedDepartment->dept_id])
                ->orderByDesc('al_created_at')->paginate(5, ['*'], 'dean_history_page')->withQueryString();
            $chairHistory = $chairHistoryAvailable
                ? DB::table('past_position_history')->where('pph_position', 'department_chair')->where('pph_scope_type', 'department')->where('pph_scope_id', $selectedDepartment->dept_id)->orderByDesc('pph_created_at')->paginate(5, ['*'], 'chair_history_page')->withQueryString()
                : Audit_Log::with('user')->where('al_target_table', 'department_chair')->where('al_target_id', $selectedDepartment->dept_id)->orderByDesc('al_created_at')->paginate(5, ['*'], 'chair_history_page')->withQueryString();
            $scheduleIds = Schedule::whereIn('sch_sec_id', $sectionIds)->pluck('sch_id');
            $studyLoadIds = Study_Load::whereIn('sl_sec_id', $sectionIds)->pluck('sl_id');
            $chairUserIds = Dept_Chair::where('dc_college_id', $assignedCollege->college_id)->pluck('dc_usr_id');
            if ($chairHistoryAvailable) {
                $chairUserIds = $chairUserIds->merge(DB::table('past_position_history')->where('pph_position', 'department_chair')->where('pph_scope_type', 'department')->where('pph_scope_id', $selectedDepartment->dept_id)
                    ->whereNotNull('pph_former_usr_id')->pluck('pph_former_usr_id'));
            }
            $chairUserIds = $chairUserIds->unique()->values();
            $departmentActivity = Audit_Log::with('user')
                ->where(function ($query) use ($selectedDepartment, $scheduleIds, $studyLoadIds, $chairUserIds): void {
                    $query->where('al_target_id', $selectedDepartment->dept_id)
                        ->orWhereIn('al_target_id', $scheduleIds)
                        ->orWhereIn('al_target_id', $studyLoadIds)
                        ->orWhere(function ($chairQuery) use ($chairUserIds): void {
                            // Middleware records removed schedules by route and actor, even after the schedule row is gone.
                            $chairQuery->whereIn('al_usr_id', $chairUserIds)
                                ->where(function ($routeQuery): void {
                                    $routeQuery->where('al_target_table', 'LIKE', '%chair.pbs%')
                                        ->orWhere('al_target_table', 'LIKE', '%chair.pbt%');
                                });
                        });
                })
                ->orderByDesc('al_created_at')->paginate(10, ['*'], 'activity_page')->withQueryString();
            $collegeFaculty = Faculty::where('fac_college_id', $assignedCollege->college_id)
                ->where('fac_is_placeholder', false)->with('user')
                ->whereHas('user', fn ($query) => $query->where('usr_role', 'faculty')->where('usr_is_active', true))
                ->orderBy('fac_last_name')->orderBy('fac_first_name')->get();
        }

        $sectionCount = $sectionCount ?? 0;
        $facultyTotal = $facultyTotal ?? 0;
        $activeFacultyCount = $activeFacultyCount ?? 0;
        $dayLoadTotal = $dayLoadTotal ?? 0;
        $eveningLoadTotal = $eveningLoadTotal ?? 0;
        return view('dean.departments', compact('deptData', 'academicYear', 'semester', 'assignedCollege', 'managedDepartments', 'selectedDepartment', 'facultyMembers', 'facultyTotal', 'sectionCount', 'activeFacultyCount', 'dayLoadTotal', 'eveningLoadTotal', 'pendingReviewCount', 'workloadAttentionCount', 'collegeFaculty', 'currentChair', 'currentDean', 'chairHistory', 'chairHistoryAvailable', 'deanHistory', 'departmentActivity'));
    }

    /**
     * Resolves a program's chair display name via department_chair -> users,
     * matching on dc_prog_id since chairs are now assigned per-program
     * (a department can have several programs, each with its own chair).
     */
    private function resolveProgramChairName(string $progId): string
    {
        $chairRecord = Dept_Chair::where('dc_dept_id', $progId)->first();
        if (!$chairRecord) {
            return '—';
        }

        $user = User::find($chairRecord->dc_usr_id);
        return $user->usr_name ?? '—';
    }

    /**
     * Maps the latest schedule_submission status to a display label + badge color.
     */
    private function resolveScheduleStatus(?Schedule_Submission $submission): array
    {
        if (!$submission) {
            return ['No Submission', 'grey'];
        }

        return match ($submission->schsub_status) {
            'submitted' => ['Submitted', 'green'],
            'pending'   => ['Pending Review', 'amber'],
            'returned'  => ['Returned', 'red'],
            default     => [ucfirst($submission->schsub_status), 'grey'],
        };
    }

    /**
     * Same OK/Near Max/Overload thresholds as individual faculty status,
     * applied to a department's average load for the summary bar color.
     * Untouched by the "not yet assigned" change — an avgLoad of 0 here
     * already reads as neutral/blue, which is fine for a summary bar.
     */
    private function resolveLoadColor(float $avgLoad): string
    {
        if ($avgLoad > self::FULL_TIME_MAX_HOURS) {
            return 'var(--red)';
        }
        if ($avgLoad >= self::FULL_TIME_MAX_HOURS - self::NEAR_MAX_BUFFER) {
            return 'var(--amber)';
        }
        return 'var(--blue)';
    }

    /**
     * Determines the badge label + color for a faculty member,
     * mirroring DeanFacultyWorkloadController's business rule.
     *
     * $hasWorkload = false means no Workload row exists for them this
     * semester at all — i.e. they haven't been given a schedule yet.
     * That's distinct from a genuine 0-hour workload record, so it's
     * checked FIRST and short-circuits the hour-based thresholds below.
     */
    private function resolveStatus(float $hours, ?string $employmentType, bool $hasWorkload = true): array
    {
        if (!$hasWorkload) {
            return ['Not Yet Assigned', 'grey'];
        }

        $maxHours = $employmentType === 'part_time'
            ? self::PART_TIME_MAX_HOURS
            : self::FULL_TIME_MAX_HOURS;
        if ($hours > $maxHours) {
            return ['Overload', 'red'];
        }
        if ($hours >= $maxHours - self::NEAR_MAX_BUFFER) {
            return ['Near Max', 'amber'];
        }
        if ($employmentType !== 'part_time' && $hours >= self::OK_THRESHOLD) {
            return ['OK', 'green'];
        }

        if ($employmentType === 'part_time') {
            return ['Part-time', 'teal'];
        }

        return ['Available', 'blue'];
    }

    /**
     * Converts raw enum values like "full_time" / "part_time"
     * into readable labels like "Full-time" / "Part-time".
     */
    private function formatEmployment(?string $type): string
    {
        return match ($type) {
            'full_time' => 'Full-time',
            'part_time' => 'Part-time',
            default     => $type ? ucfirst(str_replace('_', ' ', $type)) : '—',
        };
    }
}
