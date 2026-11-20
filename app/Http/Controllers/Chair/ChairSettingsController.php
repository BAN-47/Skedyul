<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Notification;
use App\Models\User;
use App\Services\ProfileImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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

        $filename = $chair->dc_id . '_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
        abort_unless(ProfileImageStorage::store($request->file('avatar'), 'chair_profile', $filename), 500, 'Could not save the profile photo.');
        ProfileImageStorage::delete($chair->dc_profile_image, 'chair_profile');

        $chair->update([
            'dc_profile_image' => $filename,
        ]);

        return response()->json([
            'success' => true,
            'url' => ProfileImageStorage::url($filename, 'chair_profile'),
        ]);
    }

    public function removeAvatar()
    {
        $chair = $this->currentChair();

        ProfileImageStorage::delete($chair->dc_profile_image, 'chair_profile');

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
            'fac_bachelors_degree' => 'nullable|string|max:255',
            'fac_masters_degree' => 'nullable|string|max:255',
            'fac_doctorate_degree' => 'nullable|string|max:255',
            'fac_bachelors_abbr' => [$request->filled('fac_bachelors_degree') ? 'required' : 'nullable', 'string', 'max:40'],
            'fac_masters_abbr' => [$request->filled('fac_masters_degree') ? 'required' : 'nullable', 'string', 'max:40'],
            'fac_doctorate_abbr' => [$request->filled('fac_doctorate_degree') ? 'required' : 'nullable', 'string', 'max:60'],
        ]);

        $chair = $this->currentChair();
        $chair->update(collect($data)->except([
            'fac_bachelors_degree', 'fac_masters_degree', 'fac_doctorate_degree',
            'fac_bachelors_abbr', 'fac_masters_abbr', 'fac_doctorate_abbr',
        ])->all());
        Faculty::where('fac_usr_id', Auth::id())->update([
            'fac_bachelors_degree' => $request->input('fac_bachelors_degree'),
            'fac_masters_degree' => $request->input('fac_masters_degree'),
            'fac_doctorate_degree' => $request->input('fac_doctorate_degree'),
            'fac_bachelors_abbr' => $request->input('fac_bachelors_abbr'),
            'fac_masters_abbr' => $request->input('fac_masters_abbr'),
            'fac_doctorate_abbr' => $request->input('fac_doctorate_abbr'),
        ]);

        return response()->json(['success' => true]);
    }

    public function updateContact(Request $request)
    {
        $data = $request->validate([
            'usr_email' => [
                'required', 'email', 'max:255',
                Rule::unique('USER', 'usr_email')->ignore(Auth::id(), 'usr_id'),
            ],
            'dc_phone_number' => 'nullable|string|max:30',
            'dc_address' => 'nullable|string|max:255',
            'dc_bio' => 'nullable|string|max:1000',
        ]);

        $chair = $this->currentChair();
        DB::transaction(function () use ($chair, $data) {
            Auth::user()->update(['usr_email' => $data['usr_email']]);
            $chair->update([
                'dc_phone_number' => $data['dc_phone_number'] ?? null,
                'dc_address' => $data['dc_address'] ?? null,
                'dc_bio' => $data['dc_bio'] ?? null,
            ]);
        });

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
            'faculty' => Faculty::where('fac_usr_id', Auth::id())->first(),
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

    public function reportAdminIssue(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|in:bug,system_issue,feature_request,other',
            'title' => 'required|string|max:175',
            'message' => 'required|string|max:1700',
        ]);

        abort_unless(Auth::user()?->usr_role === 'department_chair', 403);

        $admins = User::query()
            ->where('usr_role', 'system_admin')
            ->where('usr_is_active', true)
            ->pluck('usr_id');

        if ($admins->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No active system administrators can receive this message right now.',
            ], 422);
        }

        $chair = Dept_Chair::with('program')->where('dc_usr_id', Auth::id())->first();
        $user = Auth::user();
        $senderName = trim(strip_tags((string) ($user->usr_name ?: implode(' ', array_filter([
            $chair?->dc_first_name,
            $chair?->dc_last_name,
        ])))));
        $categoryLabels = [
            'bug' => 'Bug report',
            'system_issue' => 'System issue',
            'feature_request' => 'Feature request',
            'other' => 'Other feedback',
        ];
        $safeTitle = trim(strip_tags($validated['title']));
        $safeMessage = trim(strip_tags($validated['message']));
        $department = trim((string) ($chair?->program?->dept_code ?? 'Department not set'));
        $sender = $senderName ?: 'Department Chair';
        $email = trim(strip_tags((string) $user->usr_email));
        $now = now();
        $urgent = in_array($validated['category'], ['bug', 'system_issue'], true);

        $rows = $admins->map(fn ($adminId) => [
            'notif_id' => (string) Str::uuid(),
            'notif_usr_id' => $adminId,
            'notif_title' => '[Chair ' . $categoryLabels[$validated['category']] . '] ' . $safeTitle,
            'notif_message' => "From: {$sender} ({$email})\nDepartment: {$department}\n\n{$safeMessage}",
            'notif_type' => $urgent ? 'urgent' : 'info',
            'notif_is_read' => false,
            'notif_created_at' => $now,
            'notif_updated_at' => $now,
        ])->all();

        DB::table('notification')->insert($rows);

        return response()->json([
            'success' => true,
            'sent' => count($rows),
            'message' => 'Your message was sent to the system administrators.',
        ]);
    }
}
