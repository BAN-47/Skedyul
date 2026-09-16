<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));

        $users = User::query()
            ->with([
                'faculty.studyLoads.schedule.room' => function ($query) {
                    $query->select('room_id', 'room_name', 'room_building', 'room_location');
                },
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('usr_name', 'ilike', "%{$search}%")
                      ->orWhere('usr_email', 'ilike', "%{$search}%");
                });
            })
            ->orderBy('usr_name')
            ->get();

        return view('admin.user_accounts', compact('users', 'search'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        // NOTE: intentionally no Rule::unique() here. Validation-level unique
        // checks throw *before* this method's try/catch ever runs, and they
        // redirect with a separate $errors bag that this page never renders —
        // so duplicates would fail completely silently. Checking manually
        // keeps every duplicate on the same session('error') + toast path.
        $data = $request->validate([
            'usr_name'  => 'required|string|max:150',
            'usr_email' => 'required|email|max:255',
            'password'  => 'required|string|min:8',
            'usr_role'  => [
                'required',
                Rule::in(['faculty', 'department_chair', 'dean', 'system_admin']),
            ],
        ]);

        $emailTaken = User::where('usr_email', $data['usr_email'])->exists();
        $nameTaken  = User::where('usr_name', $data['usr_name'])->exists();

        if ($emailTaken) {
            return redirect()->route('admin.users')->with('error', 'This email is already registered. Please use a different email address.');
        }

        if ($nameTaken) {
            return redirect()->route('admin.users')->with('error', 'This name is already registered. Please use a different name.');
        }

        try {
            User::create([
                'usr_name'          => $data['usr_name'],
                'usr_email'         => $data['usr_email'],
                'usr_password_hash' => Hash::make($data['password']),
                'usr_role'          => $data['usr_role'],
                'usr_is_active'     => true,
            ]);
        } catch (QueryException $e) {
            // Fallback safety net for a race condition (two submits at once) —
            // the manual checks above catch the normal case.
            $sqlState = $e->errorInfo[0] ?? $e->getCode();

            if ($sqlState === '23505') {
                return redirect()->route('admin.users')->with('error', 'That name or email is already registered.');
            }

            throw $e;
        }

        return redirect()
            ->route('admin.users')
            ->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'usr_id'        => $user->usr_id,
            'usr_name'      => $user->usr_name,
            'usr_email'     => $user->usr_email,
            'usr_role'      => $user->usr_role,
            'usr_is_active' => (bool) $user->usr_is_active,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'usr_name'      => 'required|string|max:150',
            'usr_email'     => 'required|email|max:255',
            'usr_role'      => [
                'required',
                Rule::in(['faculty', 'department_chair', 'dean', 'system_admin']),
            ],
            'usr_is_active' => 'required|boolean',
        ]);

        $emailTaken = User::where('usr_email', $data['usr_email'])
            ->where('usr_id', '!=', $user->usr_id)
            ->exists();

        $nameTaken = User::where('usr_name', $data['usr_name'])
            ->where('usr_id', '!=', $user->usr_id)
            ->exists();

        if ($emailTaken) {
            return redirect()->route('admin.users')->with('error', 'This email is already registered. Please use a different email address.');
        }

        if ($nameTaken) {
            return redirect()->route('admin.users')->with('error', 'This name is already registered. Please use a different name.');
        }

        try {
            $user->update([
                'usr_name'      => $data['usr_name'],
                'usr_email'     => $data['usr_email'],
                'usr_role'      => $data['usr_role'],
                'usr_is_active' => $data['usr_is_active'],
            ]);
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? $e->getCode();

            if ($sqlState === '23505') {
                return redirect()->route('admin.users')->with('error', 'That name or email is already registered.');
            }

            throw $e;
        }

        return redirect()
            ->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        try {
            $user->delete();
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? $e->getCode();

            if ($sqlState === '23503' || str_contains(strtolower($e->getMessage()), 'foreign key')) {
                return redirect()->route('admin.users')->with('error', 'Cannot delete this user because related records still depend on it.');
            }

            throw $e;
        }

        return redirect()
            ->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'usr_is_active' => !$user->usr_is_active,
        ]);

        return back();
    }
}