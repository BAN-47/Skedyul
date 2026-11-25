<?php

namespace App\Http\Controllers\Dean;

use App\Models\Dean;
use App\Models\Faculty;
use App\Models\SystemSetting;
use App\Models\Schedule_Submission;
use App\Models\Semester;
use App\Http\Controllers\Controller;
use App\Services\ProfileImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DeanSettingsController extends Controller
{
    public function settings()
    {
        $dean = $this->currentDean();
        $academicYear = \App\Models\AcademicYear::where('ay_is_active', true)
            ->with(['semesters' => fn ($q) => $q->where('sem_is_active', true)])
            ->first();

        $activeSemester = $academicYear?->semesters->first();

        return view('dean.dean_settings', [
            'dean' => $dean,
            'college' => $dean->department,
            'faculty' => Faculty::where('fac_usr_id', Auth::id())->first(),
            'academicYear' => $academicYear,
            'activeSemester' => $activeSemester,
            'notificationPreferences' => [
                'dean_notif_faculty_overload' => (bool) SystemSetting::get(
                    $this->deanNotificationPreferenceKey((string) Auth::id(), 'dean_notif_faculty_overload'),
                    '1'
                ),
            ],
        ]);
    }

    private function deanNotificationPreferenceKey(string $userId, string $preference): string
    {
        return 'dean_notif_user_' . $userId . '_' . $preference;
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

        $filename = $dean->dean_id . '_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
        abort_unless(ProfileImageStorage::store($request->file('avatar'), 'dean_profile', $filename), 500, 'Could not save the profile photo.');
        ProfileImageStorage::delete($dean->dean_profile_image, 'dean_profile');
        $dean->update(['dean_profile_image' => $filename]);

        return response()->json(['success' => true, 'url' => ProfileImageStorage::url($filename, 'dean_profile')]);
    }

    public function removeAvatar()
    {
        $dean = $this->currentDean();

        ProfileImageStorage::delete($dean->dean_profile_image, 'dean_profile');

        $dean->update([
            'dean_profile_image' => null,
        ]);

        return response()->json(['success' => true]);
    }

    public function updatePersonalInfo(Request $request)
    {
        $data = $request->validate([
            'dean_first_name' => 'required|string|max:100|not_regex:/[<>]/',
            'dean_last_name' => 'required|string|max:100|not_regex:/[<>]/',
            'dean_middle_name' => 'nullable|string|max:100|not_regex:/[<>]/',
            'dean_suffix' => 'nullable|string|max:20|not_regex:/[<>]/',
            'dean_employee_id' => 'nullable|string|max:50|not_regex:/[<>]/',
            'dean_gender' => 'nullable|string|max:30|not_regex:/[<>]/',
            'dean_civil_status' => 'nullable|string|max:30|not_regex:/[<>]/',
            'dean_dob' => 'nullable|date',
            'dean_nationality' => 'nullable|string|max:100|not_regex:/[<>]/',
            'fac_bachelors_degree' => 'nullable|string|max:255|not_regex:/[<>]/',
            'fac_masters_degree' => 'nullable|string|max:255|not_regex:/[<>]/',
            'fac_doctorate_degree' => 'nullable|string|max:255|not_regex:/[<>]/',
            'fac_bachelors_abbr' => [$request->filled('fac_bachelors_degree') ? 'required' : 'nullable', 'string', 'max:40', 'not_regex:/[<>]/'],
            'fac_masters_abbr' => [$request->filled('fac_masters_degree') ? 'required' : 'nullable', 'string', 'max:40', 'not_regex:/[<>]/'],
            'fac_doctorate_abbr' => [$request->filled('fac_doctorate_degree') ? 'required' : 'nullable', 'string', 'max:60', 'not_regex:/[<>]/'],
        ], [
            'not_regex' => 'Angle brackets (< and >) are not allowed.',
        ]);

        $dean = $this->currentDean();
        $dean->update(collect($data)->except([
            'fac_bachelors_degree', 'fac_masters_degree', 'fac_doctorate_degree',
            'fac_bachelors_abbr', 'fac_masters_abbr', 'fac_doctorate_abbr',
        ])->all());
        Faculty::where('fac_usr_id', Auth::id())->update([
            'fac_bachelors_degree' => $data['fac_bachelors_degree'] ?? null,
            'fac_masters_degree' => $data['fac_masters_degree'] ?? null,
            'fac_doctorate_degree' => $data['fac_doctorate_degree'] ?? null,
            'fac_bachelors_abbr' => $data['fac_bachelors_abbr'] ?? null,
            'fac_masters_abbr' => $data['fac_masters_abbr'] ?? null,
            'fac_doctorate_abbr' => $data['fac_doctorate_abbr'] ?? null,
        ]);

        return response()->json(['success' => true]);
    }

    public function updateContact(Request $request)
    {
        $data = $request->validate([
            'usr_email' => [
                'required', 'string', 'max:255', 'not_regex:/[<>]/', 'email',
                Rule::unique('USER', 'usr_email')->ignore(Auth::id(), 'usr_id'),
            ],
            'dean_phone_number' => 'nullable|string|max:30|not_regex:/[<>]/',
            'dean_office_address' => 'nullable|string|max:255|not_regex:/[<>]/',
            'dean_bio' => 'nullable|string|max:1000|not_regex:/[<>]/',
        ], [
            'not_regex' => 'Angle brackets (< and >) are not allowed.',
        ]);

        $dean = $this->currentDean();
        DB::transaction(function () use ($dean, $data) {
            Auth::user()->update(['usr_email' => $data['usr_email']]);
            $dean->update([
                'dean_phone_number' => $data['dean_phone_number'] ?? null,
                'dean_address' => $data['dean_office_address'] ?? null,
                'dean_bio' => $data['dean_bio'] ?? null,
            ]);
        });

        return response()->json(['success' => true]);
    }

    public function updateInstitution(Request $request)
    {
        $data = $request->validate([
            'inst_name' => 'required|string|max:255|not_regex:/[<>]/',
            'inst_branch_campus' => 'nullable|string|max:255|not_regex:/[<>]/',
            'inst_college' => 'nullable|string|max:255|not_regex:/[<>]/',
            'inst_abbreviation' => 'nullable|string|max:50|not_regex:/[<>]/',
            'inst_contact_email' => 'nullable|string|max:150|not_regex:/[<>]/|email',
            'inst_phone' => 'nullable|string|max:30|not_regex:/[<>]/',
        ], [
            'not_regex' => 'Angle brackets (< and >) are not allowed.',
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

        $userId = (string) $request->user()->usr_id;
        foreach ($data as $key => $value) {
            SystemSetting::set(
                $this->deanNotificationPreferenceKey($userId, $key),
                $value ? '1' : '0',
                $userId
            );
        }

        return response()->json(['success' => true]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string|not_regex:/[<>]/',
            'new_password' => ['required', 'string', 'not_regex:/[<>]/', 'confirmed', Password::min(8)],
        ], [
            'not_regex' => 'Angle brackets (< and >) are not allowed.',
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
        $dean = $this->currentDean();
        $activeSemester = Semester::where('sem_is_active', true)->first();

        $query = Schedule_Submission::query()
            ->where('schsub_status', 'pending')
            ->whereIn('schsub_type', ['pbt', 'pbs'])
            ->whereHas(
                'department',
                fn ($departmentQuery) => $departmentQuery->where(
                    'dept_college_id',
                    $dean->dean_college_id
                )
            )
            ->when(
                $activeSemester,
                fn ($submissionQuery) => $submissionQuery->where(
                    'schsub_sem_id',
                    $activeSemester->sem_id
                )
            );

        return response()->json([
            'count' => $query->count(),
        ]);
    }
}
