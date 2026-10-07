<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use App\Models\Notification;
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

        // delete old file if one exists
        if ($chair->dc_profile_image) {
            $oldPath = public_path('images/chair_profile/' . $chair->dc_profile_image);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $filename = $chair->dc_id . '_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
        $request->file('avatar')->move(public_path('images/chair_profile'), $filename);

        $chair->update([
            'dc_profile_image' => $filename,
        ]);

        return response()->json([
            'success' => true,
            'url' => asset('images/chair_profile/' . $filename),
        ]);
    }

    public function removeAvatar()
    {
        $chair = $this->currentChair();

        if ($chair->dc_profile_image) {
            $path = public_path('images/chair_profile/' . $chair->dc_profile_image);
            if (file_exists($path)) {
                unlink($path);
            }
        }

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

        $userId = (string) $request->user()->usr_id;
        foreach ($data as $key => $value) {
            \App\Models\SystemSetting::set(
                $this->chairNotificationPreferenceKey($userId, $key),
                $value ? '1' : '0',
                $userId
            );
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
        $notificationPreferences = [
            'chair_notif_faculty_overload' => (bool) \App\Models\SystemSetting::get(
                $this->chairNotificationPreferenceKey((string) Auth::id(), 'chair_notif_faculty_overload'),
                '1'
            ),
        ];

        return view('chair.chair_settings', [
            'chair' => $chair,
            'departmentName' => $chair->department->dept_name ?? 'Not assigned',
            'academicYear' => \App\Models\AcademicYear::where('ay_is_active', true)->first(),
            'activeSemester' => \App\Models\Semester::where('sem_is_active', true)->first(),
            'notificationPreferences' => $notificationPreferences,
        ]);
    }

    private function chairNotificationPreferenceKey(string $userId, string $preference): string
    {
        return 'chair_notif_user_' . $userId . '_' . $preference;
    }

    public function notificationsList(Request $request)
    {
        $notifications = Notification::where('notif_usr_id', Auth::id())
            ->orderByDesc('notif_created_at')
            ->take(10)
            ->get();

        return response()->json($notifications);
    }

    public function unreadNotificationsCount()
    {
        $count = Notification::where('notif_usr_id', Auth::id())
            ->where('notif_is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}