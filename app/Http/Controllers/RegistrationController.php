<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Departments;
use App\Models\Faculty;
use App\Models\FacultyAccountReview;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class RegistrationController extends Controller
{
    public function create()
    {
        return view('index', [
            'colleges' => College::orderBy('college_name')->get(),
            'departments' => Departments::orderBy('dept_name')->get(),
            'showRegister' => true,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'usr_first_name' => ['required', 'string', 'max:150'],
            'usr_middle_name' => ['nullable', 'string', 'max:150'],
            'usr_last_name' => ['required', 'string', 'max:150'],
            'usr_suffix' => ['nullable', 'string', 'max:20'],
            'usr_employee_id' => ['nullable', 'string', 'max:255'],
            'usr_email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'usr_gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
            'usr_civil_status' => ['nullable', Rule::in(['Single', 'Married', 'Widowed', 'Separated'])],
            'usr_dob' => ['nullable', 'date', 'after_or_equal:1950-01-01', 'before_or_equal:2020-12-31'],
            'usr_nationality' => ['nullable', 'string', 'max:255'],
            'role_phone_number' => ['required', 'string', 'max:20', 'regex:/^\+[1-9]\d{1,3}\d{7,10}$/'],
            'role_address' => ['nullable', 'string', 'max:255'],
            'college_id' => ['required', 'uuid', 'exists:college,college_id'],
            'dept_id' => ['required', 'uuid', 'exists:department,dept_id'],
            'usr_rank_title' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['required', Rule::in(['full_time', 'part_time'])],
            'faculty_id_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $email = mb_strtolower(trim($data['usr_email']));
        if (User::whereRaw('LOWER(usr_email) = ?', [$email])->exists()) {
            throw ValidationException::withMessages([
                'usr_email' => 'This email is already registered.',
            ]);
        }

        $employeeId = trim($data['usr_employee_id'] ?? '');
        if ($employeeId !== '' && Faculty::whereRaw('LOWER(fac_employee_id) = ?', [mb_strtolower($employeeId)])->exists()) {
            throw ValidationException::withMessages([
                'usr_employee_id' => 'This employee ID is already registered to a faculty account.',
            ]);
        }

        $departmentBelongsToCollege = Departments::where('dept_id', $data['dept_id'])
            ->where('dept_college_id', $data['college_id'])
            ->exists();
        if (!$departmentBelongsToCollege) {
            throw ValidationException::withMessages([
                'dept_id' => 'Select a department or program that belongs to the chosen college.',
            ]);
        }

        $photoPath = $request->file('faculty_id_photo')->store('faculty-id-verification', 'local');

        try {
            DB::transaction(function () use ($data, $email, $photoPath) {
                $fullName = trim(implode(' ', array_filter([
                    $data['usr_first_name'],
                    $data['usr_middle_name'] ?? null,
                    $data['usr_last_name'],
                    $data['usr_suffix'] ?? null,
                ])));

                $user = User::create([
                    'usr_id' => (string) Str::uuid(),
                    'usr_name' => $fullName,
                    'usr_first_name' => $data['usr_first_name'],
                    'usr_middle_name' => $data['usr_middle_name'] ?? null,
                    'usr_last_name' => $data['usr_last_name'],
                    'usr_suffix' => $data['usr_suffix'] ?? null,
                    'usr_email' => $email,
                    'usr_password_hash' => Hash::make($data['password']),
                    // Public registration always requests the faculty role.
                    'usr_role' => 'faculty',
                    'usr_is_active' => false,
                ]);

                Faculty::create([
                    'fac_usr_id' => $user->usr_id,
                    'fac_college_id' => $data['college_id'],
                    'fac_dept_id' => $data['dept_id'],
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
                ]);

                FacultyAccountReview::create([
                    'fvr_id' => (string) Str::uuid(),
                    'fvr_usr_id' => $user->usr_id,
                    'fvr_id_photo_path' => $photoPath,
                    'fvr_status' => 'pending',
                ]);

                $adminIds = User::where('usr_role', 'system_admin')
                    ->where('usr_is_active', true)
                    ->pluck('usr_id');

                foreach ($adminIds as $adminId) {
                    Notification::create([
                        'notif_usr_id' => $adminId,
                        'notif_title' => 'New faculty account pending approval',
                        'notif_message' => $fullName . ' submitted a registration for review.',
                        'notif_type' => 'new_user',
                        'notif_is_read' => false,
                    ]);
                }
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($photoPath);
            throw $exception;
        }

        return redirect()->route('login')->with('registration_submitted', true);
    }
}
