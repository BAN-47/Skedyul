<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SystemSetting;
use App\Services\AuditActivityLogger;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('usr_email', $request->email)
            ->where('usr_is_active', true)
            ->first();

        if (!$user) {
            return back()->with('error', 'Account not found.');
        }

        if ($user->usr_locked_until && $user->usr_locked_until->isFuture()) {
            $minutesLeft = ceil(now()->diffInMinutes($user->usr_locked_until));
            return back()->with('error', "Account locked due to too many failed attempts. Try again in {$minutesLeft} minute(s).");
        }

        if (!Hash::check($request->password, $user->usr_password_hash)) {
            $maxAttempts = (int) SystemSetting::get('usr_max_login_attempts', 5);
            $user->usr_failed_login_attempts++;

            if ($user->usr_failed_login_attempts >= $maxAttempts) {
                $user->usr_locked_until = now()->addMinutes(15);
                $user->usr_failed_login_attempts = 0;
                $user->save();
                return back()->with('error', 'Too many failed attempts. Your account has been locked for 15 minutes.');
            }

            $user->save();
            $remaining = $maxAttempts - $user->usr_failed_login_attempts;
            return back()->with('error', "Incorrect password. {$remaining} attempt(s) remaining before lockout.");
        }

        if ($user->usr_failed_login_attempts > 0 || $user->usr_locked_until) {
            $user->usr_failed_login_attempts = 0;
            $user->usr_locked_until = null;
            $user->save();
        }

        Auth::login($user);
        AuditActivityLogger::record(
            $user,
            'Logged in',
            'session',
            'Successful login; role: ' . ($user->usr_role ?? 'user'),
            $request->ip()
        );

        switch ($user->usr_role) {
            case 'system_admin':
                return redirect()->route('admin.dashboard');
            case 'department_chair':
                return redirect()->route('chair.dashboard');
            case 'dean':
                return redirect()->route('dean.dashboard');
            case 'faculty':
                return redirect()->route('faculty.dashboard');
            default:
                Auth::logout();
                return back()->with('error', 'Invalid role.');
        }
    }

   public function logout()
    {
        if ($user = Auth::user()) {
            AuditActivityLogger::record(
                $user,
                'Logged out',
                'session',
                'User logged out; role: ' . ($user->usr_role ?? 'user'),
                request()->ip()
            );
        }

        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();


        return redirect()->route('login');

    }
}
