<?php

namespace App\Http\Controllers\Dean;

use App\Models\Dean;
use App\Models\SystemSetting;
use App\Models\Schedule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class DeanSettingsController extends Controller
{
    public function settings()
    {
        $academicYear = \App\Models\AcademicYear::where('ay_is_active', true)
            ->with(['semesters' => fn ($q) => $q->where('sem_is_active', true)])
            ->first();

        $activeSemester = $academicYear?->semesters->first();

        return view('dean.dean_settings', [
            'dean' => $this->currentDean(),
            'academicYear' => $academicYear,
            'activeSemester' => $activeSemester,
        ]);
    }

    private function currentDean(): Dean
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return $user->dean()->firstOrFail();
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $dean = $this->currentDean();

        if ($dean->dean_profile_image) {
            $oldPath = public_path('images/dean_profile/' . $dean->dean_profile_image);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $filename = $dean->dean_id . '_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
        $request->file('avatar')->move(public_path('images/dean_profile'), $filename);
        $dean->update(['dean_profile_image' => $filename]);

        return response()->json(['success' => true, 'url' => asset('images/chair_profile/' . $filename)]);
    }

    public function removeAvatar()
    {
        $dean = $this->currentDean();

        if ($dean->dean_profile_image) {
            $oldPath = public_path('images/dean_profile/' . $dean->dean_profile_image);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $dean->update([
            'dean_profile_image' => null,
        ]);

        return response()->json(['success' => true]);
    }

    public function updatePersonalInfo(Request $request)
    {
        $data = $request->validate([
            'dean_first_name' => 'required|string|max:100',
            'dean_last_name' => 'required|string|max:100',
            'dean_middle_name' => 'nullable|string|max:100',
            'dean_suffix' => 'nullable|string|max:20',
            'dean_employee_id' => 'nullable|string|max:50',
            'dean_gender' => 'nullable|string|max:30',
            'dean_civil_status' => 'nullable|string|max:30',
            'dean_dob' => 'nullable|date',
            'dean_nationality' => 'nullable|string|max:100',
        ]);

        $dean = $this->currentDean();
        $dean->update($data);

        return response()->json(['success' => true]);
    }

    public function updateContact(Request $request)
    {
        $data = $request->validate([
            'dean_gmail' => 'required|email|max:150',
            'dean_phone_number' => 'nullable|string|max:30',
            'dean_office_address' => 'nullable|string|max:255',
            'dean_bio' => 'nullable|string|max:1000',
        ]);

        $dean = $this->currentDean();
        $dean->update($data);

        return response()->json(['success' => true]);
    }

    public function updateInstitution(Request $request)
    {
        $data = $request->validate([
            'inst_name' => 'required|string|max:255',
            'inst_branch_campus' => 'nullable|string|max:255',
            'inst_college' => 'nullable|string|max:255',
            'inst_abbreviation' => 'nullable|string|max:50',
            'inst_contact_email' => 'nullable|email|max:150',
            'inst_phone' => 'nullable|string|max:30',
        ]);

        $institution = \App\Models\Institution::first();

        if ($institution) {
            $institution->update($data);
        } else {
            $data['inst_id'] = (string) \Illuminate\Support\Str::uuid();
            \App\Models\Institution::create($data);
        }

        return response()->json(['success' => true]);
    }
    
    public function updateNotificationPreferences(Request $request)
    {
        $data = $request->validate([
            'dean_notif_faculty_overload' => 'required|boolean',
        ]);

        foreach ($data as $key => $value) {
            \App\Models\SystemSetting::set($key, $value ? '1' : '0', auth()->id());
        }

        return response()->json(['success' => true]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->getAuthPassword())) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $user->update([
            'usr_password_hash' => Hash::make($request->new_password),
        ]);

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

    public function pendingCount()
    {
        return response()->json([
            'count' => Schedule::where('status', 'pending')->count(),
        ]);
    }
}