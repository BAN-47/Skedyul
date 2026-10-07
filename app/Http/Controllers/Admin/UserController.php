<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Faculty;
use App\Models\Dean;
use App\Models\Dept_Chair;
use App\Models\College;
use App\Models\Departments;
use App\Services\ScheduleAssignmentService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));

        $users = User::query()
            ->with([
                'faculty.studyLoads.schedules.room' => function ($query) {
                    $query->select(
                        'room_id',
                        'room_name',
                        'room_building',
                        'room_location'
                    );
                },
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('usr_name', 'ilike', "%{$search}%")
                        ->orWhere('usr_first_name', 'ilike', "%{$search}%")
                        ->orWhere('usr_middle_name', 'ilike', "%{$search}%")
                        ->orWhere('usr_last_name', 'ilike', "%{$search}%")
                        ->orWhere('usr_email', 'ilike', "%{$search}%");
                });
            })
            ->orderBy('usr_last_name', 'asc')
            ->orderBy('usr_first_name', 'asc')
            ->orderBy('usr_middle_name', 'asc')
            ->get();

        // Stats (full list — JS paginates the table only)
        $stats = [
            'total'    => $users->count(),
            'faculty'  => $users->where('usr_role', 'faculty')->count(),
            'chairs'   => $users->where('usr_role', 'department_chair')->count(),
            'active'   => $users->where('usr_is_active', true)->count(),
            'inactive' => $users->where('usr_is_active', false)->count(),
        ];

        $colleges    = College::orderBy('college_name')->get();
        $departments = Departments::orderBy('dept_name')->get();

        return view('admin.user_accounts', compact(
            'users',
            'search',
            'stats',
            'colleges',
            'departments'
        ));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    private function buildUserName(array $data): string
    {
        return trim(implode(' ', array_filter([
            $data['usr_first_name'] ?? null,
            $data['usr_middle_name'] ?? null,
            $data['usr_last_name'] ?? null,
            $data['usr_suffix'] ?? null,
        ])));
    }

    private function specialPositionDatabaseError(QueryException $exception): ?string
    {
        $state = $exception->errorInfo[0] ?? $exception->getCode();
        $message = strtolower($exception->getMessage());

        if ($state === '42703' && str_contains($message, 'fac_special_position')) {
            return 'Special-position columns are missing from Supabase. Add fac_special_position and fac_special_position_max_hours to public.faculty, then save again.';
        }

        if ($state === '23514' && str_contains($message, 'faculty_special_position_cap_check')) {
            return 'The saved special position and teaching-hour cap do not match. Choose the position again and save.';
        }

        return null;
    }

    private function roleSpecificRules(string $role): array
    {
        $rules = [];

        if (in_array($role, ['faculty', 'dean', 'department_chair'])) {
            $rules['role_phone_number'] = ['required', 'string', 'max:20', 'regex:/^\+[1-9]\d{1,3}\d{7,10}$/'];
            // Email is only USER.usr_email — no fac_gmail / dean_gmail / dc_gmail
            $rules['role_address'] = 'nullable|string|max:255';
            $rules['dept_id'] = 'required|uuid|exists:department,dept_id';
        }

        if (in_array($role, ['faculty', 'department_chair'])) {
            $rules['college_id'] = 'required|uuid|exists:college,college_id';
        } elseif ($role === 'dean') {
            $rules['college_id'] = 'nullable|uuid|exists:college,college_id';
        }

        if (in_array($role, ['faculty', 'dean', 'department_chair'])) {
            // Chairs and deans also have a faculty profile for schedule/load assignment.
            $rules['employment_type'] = 'required|in:full_time,part_time';
            $rules['special_position'] = [
                'nullable',
                Rule::in(array_keys(ScheduleAssignmentService::SPECIAL_POSITION_MAX_HOURS)),
            ];
        }

        if ($role === 'faculty') {
            $rules['usr_rank_title'] = 'nullable|string|max:255';
        }

        return $rules;
    }

    private function syncRoleProfile(
        User $user,
        ?string $oldRole,
        string $newRole,
        Request $request,
        array $data
    ): void {
        if ($oldRole && $oldRole !== $newRole) {
            // Keep the faculty row when changing faculty to a teaching-capable
            // leadership role. Schedules reference fac_id, so preserving this
            // record keeps their assignments and special-load settings intact.
            $preserveFacultyProfile = $oldRole === 'faculty'
                && in_array($newRole, ['department_chair', 'dean'], true);

            if (!$preserveFacultyProfile) {
                match ($oldRole) {
                    'faculty' => Faculty::where(
                        'fac_usr_id',
                        $user->usr_id
                    )->delete(),

                    'dean' => Dean::where(
                        'dean_usr_id',
                        $user->usr_id
                    )->delete(),

                    'department_chair' => Dept_Chair::where(
                        'dc_usr_id',
                        $user->usr_id
                    )->delete(),

                    default => null,
                };
            }
        }

        switch ($newRole) {
            case 'faculty':
                Faculty::updateOrCreate(
                    ['fac_usr_id' => $user->usr_id],
                    [
                        'fac_college_id' => $request->college_id,
                        'fac_dept_id' => $request->dept_id,
                        'fac_first_name' => $data['usr_first_name'],
                        'fac_middle_name' => $data['usr_middle_name'] ?? null,
                        'fac_last_name' => $data['usr_last_name'],
                        'fac_suffix' => $data['usr_suffix'] ?? null,
                        'fac_employee_id' => $data['usr_employee_id'] ?? null,
                        'fac_gender' => $data['usr_gender'] ?? null,
                        'fac_civil_status' => $data['usr_civil_status'] ?? null,
                        'fac_dob' => $data['usr_dob'] ?? null,
                        'fac_nationality' => $data['usr_nationality'] ?? null,
                        'fac_phone_number' => $request->role_phone_number,
                        'fac_address' => $request->role_address,
                        'fac_employment_type' => $request->employment_type,
                        'fac_rank' => $data['usr_rank_title'] ?? null,
                        'fac_special_position' => $data['special_position'] ?? null,
                        'fac_special_position_max_hours' => isset($data['special_position'])
                            ? ScheduleAssignmentService::SPECIAL_POSITION_MAX_HOURS[$data['special_position']]
                            : null,
                        'fac_bio' => $data['usr_bio'] ?? null,
                    ]
                );
                break;

            case 'dean':
                Dean::updateOrCreate(
                    ['dean_usr_id' => $user->usr_id],
                    [
                        'dean_college_id' => $request->college_id,
                        'dean_dept_id' => $request->dept_id,
                        'dean_first_name' => $data['usr_first_name'],
                        'dean_middle_name' => $data['usr_middle_name'] ?? null,
                        'dean_last_name' => $data['usr_last_name'],
                        'dean_suffix' => $data['usr_suffix'] ?? null,
                        'dean_employee_id' => $data['usr_employee_id'] ?? null,
                        'dean_gender' => $data['usr_gender'] ?? null,
                        'dean_civil_status' => $data['usr_civil_status'] ?? null,
                        'dean_dob' => $data['usr_dob'] ?? null,
                        'dean_nationality' => $data['usr_nationality'] ?? null,
                        'dean_phone_number' => $request->role_phone_number,
                        'dean_address' => $request->role_address,
                        'dean_bio' => $data['usr_bio'] ?? null,
                    ]
                );
                // Also register as faculty so they can be plotted on PBS/PBT
                Faculty::updateOrCreate(
                    ['fac_usr_id' => $user->usr_id],
                    [
                        'fac_college_id' => $request->college_id,
                        'fac_dept_id' => $request->dept_id,
                        'fac_first_name' => $data['usr_first_name'],
                        'fac_middle_name' => $data['usr_middle_name'] ?? null,
                        'fac_last_name' => $data['usr_last_name'],
                        'fac_suffix' => $data['usr_suffix'] ?? null,
                        'fac_employee_id' => $data['usr_employee_id'] ?? null,
                        'fac_gender' => $data['usr_gender'] ?? null,
                        'fac_civil_status' => $data['usr_civil_status'] ?? null,
                        'fac_dob' => $data['usr_dob'] ?? null,
                        'fac_nationality' => $data['usr_nationality'] ?? null,
                        'fac_phone_number' => $request->role_phone_number,
                        'fac_address' => $request->role_address,
                        'fac_employment_type' => $request->input('employment_type', 'full_time'),
                        'fac_rank' => $data['usr_rank_title'] ?? null,
                        'fac_special_position' => $data['special_position'] ?? null,
                        'fac_special_position_max_hours' => isset($data['special_position'])
                            ? ScheduleAssignmentService::SPECIAL_POSITION_MAX_HOURS[$data['special_position']]
                            : null,
                        'fac_bio' => $data['usr_bio'] ?? null,
                    ]
                );
                break;

            case 'department_chair':
                Dept_Chair::updateOrCreate(
                    ['dc_usr_id' => $user->usr_id],
                    [
                        'dc_college_id' => $request->college_id,
                        'dc_dept_id' => $request->dept_id,
                        'dc_first_name' => $data['usr_first_name'],
                        'dc_middle_name' => $data['usr_middle_name'] ?? null,
                        'dc_last_name' => $data['usr_last_name'],
                        'dc_suffix' => $data['usr_suffix'] ?? null,
                        'dc_employee_id' => $data['usr_employee_id'] ?? null,
                        'dc_gender' => $data['usr_gender'] ?? null,
                        'dc_civil_status' => $data['usr_civil_status'] ?? null,
                        'dc_dob' => $data['usr_dob'] ?? null,
                        'dc_nationality' => $data['usr_nationality'] ?? null,
                        'dc_phone_number' => $request->role_phone_number,
                        'dc_address' => $request->role_address,
                        'dc_bio' => $data['usr_bio'] ?? null,
                    ]
                );
                // Also register as faculty so they can be plotted on PBS/PBT
                Faculty::updateOrCreate(
                    ['fac_usr_id' => $user->usr_id],
                    [
                        'fac_college_id' => $request->college_id,
                        'fac_dept_id' => $request->dept_id,
                        'fac_first_name' => $data['usr_first_name'],
                        'fac_middle_name' => $data['usr_middle_name'] ?? null,
                        'fac_last_name' => $data['usr_last_name'],
                        'fac_suffix' => $data['usr_suffix'] ?? null,
                        'fac_employee_id' => $data['usr_employee_id'] ?? null,
                        'fac_gender' => $data['usr_gender'] ?? null,
                        'fac_civil_status' => $data['usr_civil_status'] ?? null,
                        'fac_dob' => $data['usr_dob'] ?? null,
                        'fac_nationality' => $data['usr_nationality'] ?? null,
                        'fac_phone_number' => $request->role_phone_number,
                        'fac_address' => $request->role_address,
                        'fac_employment_type' => $request->input('employment_type', 'full_time'),
                        'fac_rank' => $data['usr_rank_title'] ?? null,
                        'fac_special_position' => $data['special_position'] ?? null,
                        'fac_special_position_max_hours' => isset($data['special_position'])
                            ? ScheduleAssignmentService::SPECIAL_POSITION_MAX_HOURS[$data['special_position']]
                            : null,
                        'fac_bio' => $data['usr_bio'] ?? null,
                    ]
                );
                break;

            case 'system_admin':
                break;
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate(array_merge([
            'usr_first_name' => 'required|string|max:150',
            'usr_middle_name' => 'nullable|string|max:150',
            'usr_last_name' => 'required|string|max:150',
            'usr_suffix' => 'nullable|string|max:20',
            'usr_email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
            'usr_role' => ['required', Rule::in([
                'faculty',
                'department_chair',
                'dean',
                'system_admin'
            ])],
            'usr_bio' => 'nullable|string|max:1000',
            'usr_employee_id' => 'nullable|string|max:255',
            'usr_rank_title' => 'nullable|string|max:255',
            'usr_gender' => 'nullable|string|max:255',
            'usr_civil_status' => 'nullable|string|max:255',
            'usr_dob' => 'nullable|date',
            'usr_nationality' => 'nullable|string|max:255',
        ], $this->roleSpecificRules(
            $request->input('usr_role', '')
        )));

        // Case-insensitive match (Postgres treats mixed-case emails as different
        // under =, but users treat them as the same address).
        $email = strtolower(trim($data['usr_email']));
        $data['usr_email'] = $email;

        if (User::whereRaw('LOWER(usr_email) = ?', [$email])->exists()) {
            return redirect()->route('admin.users')
                ->with(
                    'error',
                    'This email is already registered. Please use a different email address.'
                );
        }

        // One department chair per program (e.g. only one BSIS chair)
        if (($data['usr_role'] ?? '') === 'department_chair' && $request->filled('dept_id')) {
            $existingChair = Dept_Chair::where('dc_dept_id', $request->dept_id)->first();
            if ($existingChair) {
                $progLabel = Departments::where('dept_id', $request->dept_id)
                    ->value('dept_code')
                    ?? 'this program';

                return redirect()->route('admin.users')
                    ->with(
                        'error',
                        "A department chair is already assigned to {$progLabel}. "
                        . 'Only one chair is allowed per program. '
                        . 'Edit or remove the existing chair first, or pick another program.'
                    );
            }
        }

        try {
            DB::transaction(function () use ($data, $request) {
                // Bio lives on role profile tables only (fac_bio / dc_bio / dean_bio).
                // Explicit usr_id so faculty.fac_usr_id is never null.
                $user = User::create([
                    'usr_id' => (string) \Illuminate\Support\Str::uuid(),
                    'usr_name' => $this->buildUserName($data),
                    'usr_first_name' => $data['usr_first_name'],
                    'usr_middle_name' => $data['usr_middle_name'] ?? null,
                    'usr_last_name' => $data['usr_last_name'],
                    'usr_suffix' => $data['usr_suffix'] ?? null,
                    'usr_email' => $data['usr_email'],
                    'usr_password_hash' => Hash::make($data['password']),
                    'usr_role' => $data['usr_role'],
                    'usr_is_active' => true,
                ]);

                $this->syncRoleProfile(
                    $user,
                    null,
                    $data['usr_role'],
                    $request,
                    $data
                );
            });
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? $e->getCode();

            if ($specialPositionError = $this->specialPositionDatabaseError($e)) {
                return redirect()->route('admin.users')->with('error', $specialPositionError);
            }

            if ($sqlState === '23505') {
                return redirect()->route('admin.users')
                    ->with('error', $this->uniqueViolationMessage($e));
            }

            throw $e;
        }

        return redirect()->route('admin.users')
            ->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $facultyProfile = Faculty::where('fac_usr_id', $id)->first();

        $profile = match ($user->usr_role) {
            'faculty' => Faculty::where(
                'fac_usr_id',
                $id
            )->first(),

            'dean' => Dean::where(
                'dean_usr_id',
                $id
            )->first(),

            'department_chair' => Dept_Chair::where(
                'dc_usr_id',
                $id
            )->first(),

            default => null,
        };

        $roleFields = match ($user->usr_role) {
            'faculty' => [
                'usr_first_name' => $user->usr_first_name,
                'usr_middle_name' => $user->usr_middle_name,
                'usr_last_name' => $user->usr_last_name,
                'usr_suffix' => $user->usr_suffix,
                'usr_employee_id' => $profile->fac_employee_id ?? null,
                'usr_rank_title' => $profile->fac_rank ?? null,
                'usr_gender' => $profile->fac_gender ?? null,
                'usr_civil_status' => $profile->fac_civil_status ?? null,
                'usr_dob' => $profile->fac_dob ?? null,
                'usr_nationality' => $profile->fac_nationality ?? null,
                'role_phone_number' => $profile->fac_phone_number ?? null,
                'role_address' => $profile->fac_address ?? null,
                'college_id' => $profile->fac_college_id ?? null,
                'dept_id' => $profile->fac_dept_id ?? null,
                'employment_type' => $facultyProfile->fac_employment_type ?? null,
                'special_position' => $facultyProfile->fac_special_position ?? null,
                'usr_bio' => $profile->fac_bio ?? null,
            ],

            'dean' => [
                'usr_first_name' => $user->usr_first_name,
                'usr_middle_name' => $user->usr_middle_name,
                'usr_last_name' => $user->usr_last_name,
                'usr_suffix' => $user->usr_suffix,
                'usr_employee_id' => $profile->dean_employee_id ?? null,
                'usr_rank_title' => null,
                'usr_gender' => $profile->dean_gender ?? null,
                'usr_civil_status' => $profile->dean_civil_status ?? null,
                'usr_dob' => $profile->dean_dob ?? null,
                'usr_nationality' => $profile->dean_nationality ?? null,
                'role_phone_number' => $profile->dean_phone_number ?? null,
                'role_address' => $profile->dean_address ?? null,
                'college_id' => $profile->dean_college_id ?? null,
                'dept_id' => $profile->dean_dept_id ?? null,
                'employment_type' => $facultyProfile->fac_employment_type ?? null,
                'special_position' => $facultyProfile->fac_special_position ?? null,
                'usr_bio' => $profile->dean_bio ?? null,
            ],

            'department_chair' => [
                'usr_first_name' => $user->usr_first_name,
                'usr_middle_name' => $user->usr_middle_name,
                'usr_last_name' => $user->usr_last_name,
                'usr_suffix' => $user->usr_suffix,
                'usr_employee_id' => $profile->dc_employee_id ?? null,
                'usr_rank_title' => null,
                'usr_gender' => $profile->dc_gender ?? null,
                'usr_civil_status' => $profile->dc_civil_status ?? null,
                'usr_dob' => $profile->dc_dob ?? null,
                'usr_nationality' => $profile->dc_nationality ?? null,
                'role_phone_number' => $profile->dc_phone_number ?? null,
                'role_address' => $profile->dc_address ?? null,
                'college_id' => $profile->dc_college_id ?? null,
                'dept_id' => $profile->dc_dept_id ?? null,
                'employment_type' => $facultyProfile->fac_employment_type ?? null,
                'special_position' => $facultyProfile->fac_special_position ?? null,
                'usr_bio' => $profile->dc_bio ?? null,
            ],

            default => [
                'usr_first_name' => $user->usr_first_name,
                'usr_middle_name' => $user->usr_middle_name,
                'usr_last_name' => $user->usr_last_name,
                'usr_suffix' => $user->usr_suffix,
                'usr_employee_id' => null,
                'usr_rank_title' => null,
                'usr_gender' => null,
                'usr_civil_status' => null,
                'usr_dob' => null,
                'usr_nationality' => null,
                'role_phone_number' => null,
                'role_address' => null,
                'college_id' => null,
                'dept_id' => null,
                'employment_type' => null,
                'usr_bio' => null,
            ],
        };

        $roleFields['special_position'] ??= $facultyProfile->fac_special_position ?? null;

        return response()->json(array_merge([
            'usr_id' => $user->usr_id,
            'usr_name' => $this->buildUserName([
                'usr_first_name' => $user->usr_first_name,
                'usr_middle_name' => $user->usr_middle_name,
                'usr_last_name' => $user->usr_last_name,
                'usr_suffix' => $user->usr_suffix,
            ]),
            'usr_first_name' => $user->usr_first_name,
            'usr_middle_name' => $user->usr_middle_name,
            'usr_last_name' => $user->usr_last_name,
            'usr_suffix' => $user->usr_suffix,
            'usr_email' => $user->usr_email,
            'usr_role' => $user->usr_role,
            'usr_is_active' => (bool) $user->usr_is_active,
        ], $roleFields));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $oldRole = $user->usr_role;

        $data = $request->validate(array_merge([
            'usr_first_name' => 'required|string|max:150',
            'usr_middle_name' => 'nullable|string|max:150',
            'usr_last_name' => 'required|string|max:150',
            'usr_suffix' => 'nullable|string|max:20',
            'usr_email' => 'required|email|max:255',
            'usr_role' => ['required', Rule::in([
                'faculty',
                'department_chair',
                'dean',
                'system_admin'
            ])],
            'usr_is_active' => 'required|boolean',
            'usr_bio' => 'nullable|string|max:1000',
            'usr_employee_id' => 'nullable|string|max:255',
            'usr_rank_title' => 'nullable|string|max:255',
            'usr_gender' => 'nullable|string|max:255',
            'usr_civil_status' => 'nullable|string|max:255',
            'usr_dob' => 'nullable|date',
            'usr_nationality' => 'nullable|string|max:255',
        ], $this->roleSpecificRules(
            $request->input('usr_role', '')
        )));

        $email = strtolower(trim($data['usr_email']));
        $data['usr_email'] = $email;

        $emailTaken = User::whereRaw('LOWER(usr_email) = ?', [$email])
            ->where('usr_id', '!=', $user->usr_id)
            ->exists();

        if ($emailTaken) {
            return redirect()->route('admin.users')
                ->with(
                    'error',
                    'This email is already registered. Please use a different email address.'
                );
        }

        // Enforce one chair per program when changing an existing account's role.
        // Excluding this account lets the current chair save edits to their own profile.
        if (($data['usr_role'] ?? '') === 'department_chair' && $request->filled('dept_id')) {
            $existingChair = Dept_Chair::where('dc_dept_id', $request->dept_id)
                ->where('dc_usr_id', '!=', $user->usr_id)
                ->first();

            if ($existingChair) {
                $programLabel = Departments::where('dept_id', $request->dept_id)
                    ->value('dept_code')
                    ?? 'the selected program';

                return redirect()->route('admin.users')
                    ->with(
                        'error',
                        "Cannot assign this account as the {$programLabel} department chair. "
                        . 'That program already has a department chair. The account role was not changed; edit or remove the current chair first, or select another program.'
                    );
            }
        }

        try {
            DB::transaction(function () use (
                $user,
                $data,
                $request,
                $oldRole
            ) {
                // Bio lives on role profile tables only (fac_bio / dc_bio / dean_bio).
                $user->update([
                    'usr_name' => $this->buildUserName($data),
                    'usr_first_name' => $data['usr_first_name'],
                    'usr_middle_name' => $data['usr_middle_name'] ?? null,
                    'usr_last_name' => $data['usr_last_name'],
                    'usr_suffix' => $data['usr_suffix'] ?? null,
                    'usr_email' => $data['usr_email'],
                    'usr_role' => $data['usr_role'],
                    'usr_is_active' => $data['usr_is_active'],
                ]);

                $this->syncRoleProfile(
                    $user,
                    $oldRole,
                    $data['usr_role'],
                    $request,
                    $data
                );
            });
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? $e->getCode();

            if ($specialPositionError = $this->specialPositionDatabaseError($e)) {
                return redirect()->route('admin.users')->with('error', $specialPositionError);
            }

            if ($sqlState === '23505') {
                return redirect()->route('admin.users')
                    ->with('error', $this->uniqueViolationMessage($e));
            }

            throw $e;
        }

        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        try {
            $user->delete();
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? $e->getCode();

            if (
                $sqlState === '23503' ||
                str_contains(strtolower($e->getMessage()), 'foreign key')
            ) {
                return redirect()->route('admin.users')
                    ->with(
                        'error',
                        'Cannot delete this user because related records still depend on it.'
                    );
            }

            throw $e;
        }

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'usr_is_active' => !$user->usr_is_active,
        ]);

        return back();
    }

    /**
     * Map Postgres unique_violation (23505) to a clear admin message.
     * Previously every unique clash was reported as "email already registered",
     * which was wrong when the clash was employee_id, phone, etc.
     */
    private function uniqueViolationMessage(\Illuminate\Database\QueryException $e): string
    {
        $detail = strtolower($e->getMessage());

        if (str_contains($detail, 'usr_email') || str_contains($detail, 'email')) {
            return 'This email is already registered. Please use a different email address.';
        }
        if (str_contains($detail, 'employee')) {
            return 'This employee ID is already in use. Please use a different employee ID.';
        }
        if (str_contains($detail, 'phone')) {
            return 'This phone number is already in use.';
        }
        // UNIQUE on department_chair.dc_dept_id → one chair per department
        if (
            str_contains($detail, 'dc_dept_id')
            || str_contains($detail, 'department_chair')
            || (str_contains($detail, 'prog') && str_contains($detail, 'chair'))
        ) {
            return 'A department chair is already assigned to that program. '
                . 'Only one chair is allowed per department. '
                . 'Edit or remove the existing chair first, or pick another program.';
        }

        return 'A record with the same unique value already exists. Check email, employee ID, phone, or program assignment.';
    }

}
