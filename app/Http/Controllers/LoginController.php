<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

       $user = User::where('usr_email',$request->email)
            ->where('usr_is_active',true)
            ->first();

        if (!$user) {
            return back()->with('error', 'Account not found.');
        }

        if (!Hash::check($request->password, $user->usr_password_hash)) {
            return back()->with('error', 'Incorrect password.');
        }

        Auth::login($user);

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

        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();


        return redirect()->route('login');

    }
}