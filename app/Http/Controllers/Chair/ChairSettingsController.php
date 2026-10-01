<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ChairSettingsController extends Controller
{
    private function currentChair(): Dept_Chair
    {
        return Dept_Chair::where('dc_usr_id', Auth::id())->firstOrFail();
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $chair = $this->currentChair();
        $publicId = 'chair_' . $chair->dc_id;

        Storage::disk('cloudinary')->delete('skedyul/chair-avatars/' . $publicId);

        $path = $request->file('avatar')->storeAs('skedyul/chair-avatars', $publicId, 'cloudinary');

        $chair->update([
            'dc_profile_image' => Storage::disk('cloudinary')->url($path),
        ]);

        return response()->json(['success' => true, 'url' => Storage::disk('cloudinary')->url($path)]);
    }

    public function removeAvatar()
    {
        $chair = $this->currentChair();
        $publicId = 'chair_' . $chair->dc_id;

        Storage::disk('cloudinary')->delete('skedyul/chair-avatars/' . $publicId);

        $chair->update(['dc_profile_image' => null]);

        return response()->json(['success' => true]);
    }

    public function updatePersonalInfo(Request $request)
    {
        $data = $request->validate([
            'dc_first_name' => 'required|string|max:100',
            'dc_last_name' => 'required|string|max:100',
            'dc_middle_name' => 'nullable|string|max:100',
            'dc_suffix' => 'nullable|string|max:20',
            'dc_employee_id' => 'nullable|string|max:50',
            'dc_gender' => 'nullable|string|max:30',
            'dc_civil_status' => 'nullable|string|max:30',
            'dc_dob' => 'nullable|date',
            'dc_nationality' => 'nullable|string|max:100',
        ]);

        $chair = $this->currentChair();
        $chair->update($data);

        return response()->json(['success' => true]);
    }

    public function updateContact(Request $request)
    {
        $data = $request->validate([
            'dc_gmail' => 'required|email|max:150',
            'dc_phone_number' => 'nullable|string|max:30',
            'dc_address' => 'nullable|string|max:255',
            'dc_bio' => 'nullable|string|max:1000',
        ]);

        $chair = $this->currentChair();
        $chair->update($data);

        return response()->json(['success' => true]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->usr_password_hash)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update(['usr_password_hash' => Hash::make($request->new_password)]);

        return response()->json(['success' => true]);
    }

    public function updateNotificationPreferences(Request $request)
    {
        $data = $request->validate([
            'chair_notif_faculty_overload' => 'sometimes|required|boolean',
        ]);

        foreach ($data as $key => $value) {
            \App\Models\SystemSetting::set($key, $value ? '1' : '0', Auth::id());
        }

        return response()->json(['success' => true]);
    }

    public function updateSecuritySettings(Request $request)
    {
        $validated = $request->validate([
            'usr_session_timeout_minutes' => 'required|integer|in:15,30,60,999999',
            'usr_max_login_attempts' => 'required|integer|in:3,5,10',
        ]);

        foreach ($validated as $key => $value) {
            \App\Models\SystemSetting::set($key, $value, Auth::id());
        }

        return response()->json(['success' => true]);
    }

    public function settings()
    {
        $chair = $this->currentChair()->load(['department']);

        return view('chair.settings', [
            'chair' => $chair,
            'departmentName' => $chair->department->dept_name ?? 'Not assigned',
            'academicYear' => \App\Models\AcademicYear::where('ay_is_active', true)->first(),
            'activeSemester' => \App\Models\Semester::where('sem_is_active', true)->first(),
        ]);
    }
}