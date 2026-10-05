<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class FacultySettingsController extends Controller
{
    private function currentFaculty(): Faculty
    {
        return Faculty::where('fac_usr_id', Auth::id())->firstOrFail();
    }

    public function settings()
    {
        $faculty = $this->currentFaculty()->load(['department', 'program']);
        $user = Auth::user();
        $notificationPreferences = [
            'faculty_notif_schedule_updates' => true,
            'faculty_notif_new_assignments' => true,
            'faculty_notif_reminders' => true,
            'faculty_notif_system_announcements' => false,
        ];

        foreach ($notificationPreferences as $key => $default) {
            $notificationPreferences[$key] = (bool) SystemSetting::get($key, $default ? '1' : '0');
        }

        return view('faculty.faculty_settings', [
            'faculty' => $faculty,
            'user' => $user,
            'sessionTimeout' => (int) SystemSetting::get('usr_session_timeout_minutes', $user->usr_session_timeout_minutes ?? 30),
            'maxLoginAttempts' => (int) SystemSetting::get('usr_max_login_attempts', $user->usr_max_login_attempts ?? 5),
            'notificationPreferences' => $notificationPreferences,
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $faculty = $this->currentFaculty();
        $directory = public_path('images/faculty_profile');
        File::ensureDirectoryExists($directory);

        $filename = $faculty->fac_id . '_' . Str::uuid() . '.' . $request->file('avatar')->getClientOriginalExtension();
        $request->file('avatar')->move($directory, $filename);

        $oldImage = $faculty->fac_profile_image;
        $faculty->update(['fac_profile_image' => $filename]);

        if ($oldImage) {
            $oldPath = $directory . DIRECTORY_SEPARATOR . basename($oldImage);
            if (is_file($oldPath)) {
                unlink($oldPath);
            }
        }

        return response()->json([
            'success' => true,
            'url' => asset('images/faculty_profile/' . $filename),
        ]);
    }

    public function removeAvatar()
    {
        $faculty = $this->currentFaculty();
        $oldImage = $faculty->fac_profile_image;
        $faculty->update(['fac_profile_image' => null]);

        if ($oldImage) {
            $oldPath = public_path('images/faculty_profile/' . basename($oldImage));
            if (is_file($oldPath)) {
                unlink($oldPath);
            }
        }

        return response()->json(['success' => true]);
    }

    public function updatePersonalInfo(Request $request)
    {
        $user    = $request->user();
        $faculty = Faculty::where('fac_usr_id', $user->usr_id)->firstOrFail();

        $data = $request->validate([
            'fac_first_name'   => ['required', 'string', 'max:255'],
            'fac_last_name'    => ['required', 'string', 'max:255'],
            'fac_middle_name'  => ['nullable', 'string', 'max:255'],
            'fac_suffix'       => ['nullable', 'string', 'max:50'],
            'fac_employee_id'  => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('faculty', 'fac_employee_id')->ignore($faculty->fac_id, 'fac_id')
            ],
            'fac_gender'       => ['nullable', 'in:Male,Female,Prefer not to say'],
            'fac_civil_status' => ['nullable', 'in:Single,Married,Widowed'],
            'fac_dob'          => ['nullable', 'date'],
            'fac_nationality'  => ['nullable', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($user, $faculty, $data) {
            $faculty->update($data);

            $user->update([
                'usr_first_name'  => $data['fac_first_name'],
                'usr_middle_name' => $data['fac_middle_name'] ?? null,
                'usr_last_name'   => $data['fac_last_name'],
                'usr_suffix'      => $data['fac_suffix'] ?? null,
            ]);
        });

        return response()->json(['message' => 'Personal information saved.']);
    }

    public function updateContact(Request $request)
    {
        $user    = $request->user();
        $faculty = Faculty::where('fac_usr_id', $user->usr_id)->firstOrFail();

        $data = $request->validate([
            'usr_email'        => [
                'required',
                'email',
                'max:255',
                Rule::unique('USER', 'usr_email')->ignore($user->usr_id, 'usr_id')
            ],
            'fac_phone_number' => ['nullable', 'string', 'max:50'],
            'fac_address'      => ['nullable', 'string', 'max:255'],
            'fac_bio'          => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($user, $faculty, $data) {
            $user->update(['usr_email' => $data['usr_email']]);
            $faculty->update([
                'fac_phone_number' => $data['fac_phone_number'] ?? null,
                'fac_address'      => $data['fac_address'] ?? null,
                'fac_bio'          => $data['fac_bio'] ?? null,
            ]);
        });

        return response()->json(['message' => 'Contact details saved.']);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->getAuthPassword())) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update(['usr_password_hash' => Hash::make($request->new_password)]);

        return response()->json(['success' => true]);
    }

    public function updateSecuritySettings(Request $request)
    {
        $validated = $request->validate([
            'usr_session_timeout_minutes' => 'required|integer|in:15,30,60,999999',
            'usr_max_login_attempts' => 'required|integer|in:3,5,10',
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::set($key, $value, Auth::id());
        }

        return response()->json(['success' => true]);
    }

    public function updateNotificationPreferences(Request $request)
    {
        $validated = $request->validate([
            'faculty_notif_schedule_updates' => 'required|boolean',
            'faculty_notif_new_assignments' => 'required|boolean',
            'faculty_notif_reminders' => 'required|boolean',
            'faculty_notif_system_announcements' => 'required|boolean',
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::set($key, $value ? '1' : '0', Auth::id());
        }

        return response()->json(['success' => true]);
    }
}
