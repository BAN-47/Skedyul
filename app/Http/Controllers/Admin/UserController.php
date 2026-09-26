<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Faculty;
use App\Models\Dean;
use App\Models\DepartmentChair;
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
                'faculty.studyLoads.schedule.room' => function ($query) {
                    $query->select('room_id', 'room_name', 'room_building', 'room_location');
                },
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('usr_name', 'ilike', "%{$search}%")
                      ->orWhere('usr_email', 'ilike', "%{$search}%");
                });
            })
            ->orderBy('usr_name')
            ->get();

        // Needed for the Department / Program dropdowns in the Add + Edit modals.
        $departments = DB::table('department')->orderBy('dept_name')->get();
        $programs    = DB::table('program')->orderBy('prog_name')->get();

        return view('admin.user_accounts', compact('users', 'search', 'departments', 'programs'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Fields that only apply depending on usr_role. Faculty/Dean/Chair all
     * require phone_number (NOT NULL in their tables); faculty additionally
     * requires employment_type; dept is required for faculty + chair
     * (NOT NULL in DB) but optional for dean; program only applies to chairs.
     */
    private function roleSpecificRules(string $role): array
    {
        $rules = [];

        if (in_array($role, ['faculty', 'dean', 'department_chair'])) {
            $rules['role_phone_number'] = 'required|string|max:20';
            $rules['role_gmail']        = 'nullable|email|max:255';
            $rules['role_address']      = 'nullable|string|max:255';
        }

        if (in_array($role, ['faculty', 'department_chair'])) {
            $rules['dept_id'] = 'required|uuid|exists:department,dept_id';
        } elseif ($role === 'dean') {
            $rules['dept_id'] = 'nullable|uuid|exists:department,dept_id';
        }

        if ($role === 'department_chair') {
            $rules['prog_id'] = 'nullable|uuid|exists:program,prog_id';
        }

        if ($role === 'faculty') {
            $rules['employment_type'] = 'required|in:full_time,part_time';
        }

        return $rules;
    }

    /**
     * Creates/updates/removes the role-specific profile row for a user.
     * If the role changed, the OLD role's row is deleted (safe — a brand
     * new role has no schedules/study_loads yet). If the role is unchanged,
     * updateOrCreate() keeps the existing fac_id/dean_id/dc_id in place so
     * FK-linked records (schedules, study loads, etc.) are never disturbed.
     */
    private function syncRoleProfile(User $user, ?string $oldRole, string $newRole, Request $request, array $data): void
    {
        if ($oldRole && $oldRole !== $newRole) {
            match ($oldRole) {
                'faculty'           => Faculty::where('fac_usr_id', $user->usr_id)->delete(),
                'dean'              => Dean::where('dean_usr_id', $user->usr_id)->delete(),
                'department_chair'  => DepartmentChair::where('dc_usr_id', $user->usr_id)->delete(),
                default             => null,
            };
        }

        switch ($newRole) {
            case 'faculty':
                Faculty::updateOrCreate(
                    ['fac_usr_id' => $user->usr_id],
                    [
                        'fac_dept_id'         => $request->dept_id,
                        'fac_first_name'      => $data['usr_first_name'],
                        'fac_middle_name'     => $data['usr_middle_name'] ?? null,
                        'fac_last_name'       => $data['usr_last_name'],
                        'fac_suffix'          => $data['usr_suffix'] ?? null,
                        'fac_phone_number'    => $request->role_phone_number,
                        'fac_gmail'           => $request->role_gmail,
                        'fac_address'         => $request->role_address,
                        'fac_employment_type' => $request->employment_type,
                        'fac_rank'            => $data['usr_rank_title'] ?? null,
                    ]
                );
                break;

            case 'dean':
                Dean::updateOrCreate(
                    ['dean_usr_id' => $user->usr_id],
                    [
                        'dean_dept_id'      => $request->dept_id,
                        'dean_first_name'   => $data['usr_first_name'],
                        'dean_middle_name'  => $data['usr_middle_name'] ?? null,
                        'dean_last_name'    => $data['usr_last_name'],
                        'dean_suffix'       => $data['usr_suffix'] ?? null,
                        'dean_phone_number' => $request->role_phone_number,
                        'dean_gmail'        => $request->role_gmail,
                        'dean_address'      => $request->role_address,
                    ]
                );
                break;

            case 'department_chair':
                DepartmentChair::updateOrCreate(
                    ['dc_usr_id' => $user->usr_id],
                    [
                        'dc_dept_id'      => $request->dept_id,
                        'dc_prog_id'      => $request->prog_id,
                        'dc_first_name'   => $data['usr_first_name'],
                        'dc_middle_name'  => $data['usr_middle_name'] ?? null,
                        'dc_last_name'    => $data['usr_last_name'],
                        'dc_suffix'       => $data['usr_suffix'] ?? null,
                        'dc_phone_number' => $request->role_phone_number,
                        'dc_gmail'        => $request->role_gmail,
                        'dc_address'      => $request->role_address,
                    ]
                );
                break;

            case 'system_admin':
                // No profile table for system_admin.
                break;
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate(array_merge([
            'usr_first_name'   => 'required|string|max:150',
            'usr_middle_name'  => 'nullable|string|max:150',
            'usr_last_name'    => 'required|string|max:150',
            'usr_suffix'       => 'nullable|string|max:20',
            'usr_email'        => 'required|email|max:255',
            'password'         => 'required|string|min:8',
            'usr_role'         => ['required', Rule::in(['faculty', 'department_chair', 'dean', 'system_admin'])],

            'usr_employee_id'  => 'nullable|string|max:255',
            'usr_rank_title'   => 'nullable|string|max:255',
            'usr_gender'       => 'nullable|string|max:255',
            'usr_civil_status' => 'nullable|string|max:255',
            'usr_dob'          => 'nullable|date',
            'usr_nationality'  => 'nullable|string|max:255',
        ], $this->roleSpecificRules($request->input('usr_role', ''))));

        $usrName = trim(implode(' ', array_filter([
            $data['usr_first_name'],
            $data['usr_middle_name'] ?? null,
            $data['usr_last_name'],
            $data['usr_suffix'] ?? null,
        ])));

        // NOTE: intentionally no Rule::unique() — see original comment: keeps
        // duplicate errors on the same session('error') + toast path.
        if (User::where('usr_email', $data['usr_email'])->exists()) {
            return redirect()->route('admin.users')->with('error', 'This email is already registered. Please use a different email address.');
        }
        if (User::where('usr_name', $usrName)->exists()) {
            return redirect()->route('admin.users')->with('error', 'This name is already registered. Please use a different name.');
        }

        try {
            DB::transaction(function () use ($data, $request, $usrName) {
                $user = User::create([
                    'usr_name'          => $usrName,
                    'usr_email'         => $data['usr_email'],
                    'usr_password_hash' => Hash::make($data['password']),
                    'usr_role'          => $data['usr_role'],
                    'usr_is_active'     => true,

                    'usr_first_name'    => $data['usr_first_name'],
                    'usr_middle_name'   => $data['usr_middle_name'] ?? null,
                    'usr_last_name'     => $data['usr_last_name'],
                    'usr_suffix'        => $data['usr_suffix'] ?? null,
                    'usr_employee_id'   => $data['usr_employee_id'] ?? null,
                    'usr_rank_title'    => $data['usr_rank_title'] ?? null,
                    'usr_gender'        => $data['usr_gender'] ?? null,
                    'usr_civil_status'  => $data['usr_civil_status'] ?? null,
                    'usr_dob'           => $data['usr_dob'] ?? null,
                    'usr_nationality'   => $data['usr_nationality'] ?? null,
                ]);

                $this->syncRoleProfile($user, null, $data['usr_role'], $request, $data);
            });
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? $e->getCode();

            if ($sqlState === '23505') {
                return redirect()->route('admin.users')->with('error', 'That name or email is already registered.');
            }

            throw $e;
        }

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $profile = match ($user->usr_role) {
            'faculty'          => Faculty::where('fac_usr_id', $id)->first(),
            'dean'             => Dean::where('dean_usr_id', $id)->first(),
            'department_chair' => DepartmentChair::where('dc_usr_id', $id)->first(),
            default            => null,
        };

        $roleFields = match ($user->usr_role) {
            'faculty' => [
                'role_phone_number' => $profile->fac_phone_number ?? null,
                'role_gmail'        => $profile->fac_gmail ?? null,
                'role_address'      => $profile->fac_address ?? null,
                'dept_id'           => $profile->fac_dept_id ?? null,
                'employment_type'   => $profile->fac_employment_type ?? null,
                'prog_id'           => null,
            ],
            'dean' => [
                'role_phone_number' => $profile->dean_phone_number ?? null,
                'role_gmail'        => $profile->dean_gmail ?? null,
                'role_address'      => $profile->dean_address ?? null,
                'dept_id'           => $profile->dean_dept_id ?? null,
                'employment_type'   => null,
                'prog_id'           => null,
            ],
            'department_chair' => [
                'role_phone_number' => $profile->dc_phone_number ?? null,
                'role_gmail'        => $profile->dc_gmail ?? null,
                'role_address'      => $profile->dc_address ?? null,
                'dept_id'           => $profile->dc_dept_id ?? null,
                'employment_type'   => null,
                'prog_id'           => $profile->dc_prog_id ?? null,
            ],
            default => [
                'role_phone_number' => null, 'role_gmail' => null, 'role_address' => null,
                'dept_id' => null, 'employment_type' => null, 'prog_id' => null,
            ],
        };

        return response()->json(array_merge([
            'usr_id'           => $user->usr_id,
            'usr_name'         => $user->usr_name,
            'usr_email'        => $user->usr_email,
            'usr_role'         => $user->usr_role,
            'usr_is_active'    => (bool) $user->usr_is_active,
            'usr_first_name'   => $user->usr_first_name,
            'usr_middle_name'  => $user->usr_middle_name,
            'usr_last_name'    => $user->usr_last_name,
            'usr_suffix'       => $user->usr_suffix,
            'usr_employee_id'  => $user->usr_employee_id,
            'usr_rank_title'   => $user->usr_rank_title,
            'usr_gender'       => $user->usr_gender,
            'usr_civil_status' => $user->usr_civil_status,
            'usr_dob'          => $user->usr_dob,
            'usr_nationality'  => $user->usr_nationality,
            'usr_bio'          => $user->usr_bio,
        ], $roleFields));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $oldRole = $user->usr_role;

        $data = $request->validate(array_merge([
            'usr_first_name'   => 'required|string|max:150',
            'usr_middle_name'  => 'nullable|string|max:150',
            'usr_last_name'    => 'required|string|max:150',
            'usr_suffix'       => 'nullable|string|max:20',
            'usr_email'        => 'required|email|max:255',
            'usr_role'         => ['required', Rule::in(['faculty', 'department_chair', 'dean', 'system_admin'])],
            'usr_is_active'    => 'required|boolean',

            'usr_employee_id'  => 'nullable|string|max:255',
            'usr_rank_title'   => 'nullable|string|max:255',
            'usr_gender'       => 'nullable|string|max:255',
            'usr_civil_status' => 'nullable|string|max:255',
            'usr_dob'          => 'nullable|date',
            'usr_nationality'  => 'nullable|string|max:255',
            'usr_bio'          => 'nullable|string',
        ], $this->roleSpecificRules($request->input('usr_role', ''))));

        $usrName = trim(implode(' ', array_filter([
            $data['usr_first_name'],
            $data['usr_middle_name'] ?? null,
            $data['usr_last_name'],
            $data['usr_suffix'] ?? null,
        ])));

        $emailTaken = User::where('usr_email', $data['usr_email'])->where('usr_id', '!=', $user->usr_id)->exists();
        $nameTaken  = User::where('usr_name', $usrName)->where('usr_id', '!=', $user->usr_id)->exists();

        if ($emailTaken) {
            return redirect()->route('admin.users')->with('error', 'This email is already registered. Please use a different email address.');
        }
        if ($nameTaken) {
            return redirect()->route('admin.users')->with('error', 'This name is already registered. Please use a different name.');
        }

        try {
            DB::transaction(function () use ($user, $data, $request, $usrName, $oldRole) {
                $user->update([
                    'usr_name'          => $usrName,
                    'usr_email'         => $data['usr_email'],
                    'usr_role'          => $data['usr_role'],
                    'usr_is_active'     => $data['usr_is_active'],

                    'usr_first_name'    => $data['usr_first_name'],
                    'usr_middle_name'   => $data['usr_middle_name'] ?? null,
                    'usr_last_name'     => $data['usr_last_name'],
                    'usr_suffix'        => $data['usr_suffix'] ?? null,
                    'usr_employee_id'   => $data['usr_employee_id'] ?? null,
                    'usr_rank_title'    => $data['usr_rank_title'] ?? null,
                    'usr_gender'        => $data['usr_gender'] ?? null,
                    'usr_civil_status'  => $data['usr_civil_status'] ?? null,
                    'usr_dob'           => $data['usr_dob'] ?? null,
                    'usr_nationality'   => $data['usr_nationality'] ?? null,
                    'usr_bio'           => $data['usr_bio'] ?? null,
                ]);

                $this->syncRoleProfile($user, $oldRole, $data['usr_role'], $request, $data);
            });
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? $e->getCode();

            if ($sqlState === '23505') {
                return redirect()->route('admin.users')->with('error', 'That name or email is already registered.');
            }

            throw $e;
        }

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        try {
            $user->delete();
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? $e->getCode();

            if ($sqlState === '23503' || str_contains(strtolower($e->getMessage()), 'foreign key')) {
                return redirect()->route('admin.users')->with('error', 'Cannot delete this user because related records still depend on it.');
            }

            throw $e;
        }

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'usr_is_active' => !$user->usr_is_active,
        ]);

        return back();
    }
}