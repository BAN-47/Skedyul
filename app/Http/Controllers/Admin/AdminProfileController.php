<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminProfileController extends Controller
{
    public function updatePersonalInfo(Request $request)
    {
        $validated = $request->validate([
            'usr_first_name' => 'required|string|max:100',
            'usr_last_name' => 'required|string|max:100',
            'usr_middle_name' => 'nullable|string|max:100',
            'usr_suffix' => 'nullable|string|max:10',
        ]);

        $user = Auth::user();
        $user->update($validated);

        return response()->json([
            'success' => true,
            'usr_name' => $user->usr_name,
        ]);
    }

    public function updateNotificationPreferences(Request $request)
    {
        $validated = $request->validate([
            'notif_new_user_registration' => 'sometimes|required|boolean',
            'notif_faculty_overload' => 'sometimes|required|boolean',
            'notif_system_backups' => 'sometimes|required|boolean',
            'notif_login_activity' => 'sometimes|required|boolean',
        ]);

        Auth::user()->update($validated);

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
                'current_password' => ['The current password is incorrect. Please try again.'],
            ]);
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

        Auth::user()->update($validated);

        return response()->json(['success' => true]);
    }
}