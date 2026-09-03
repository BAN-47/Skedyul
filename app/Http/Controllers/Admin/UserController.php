<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        $data = $request->validate([
            'usr_name' => 'required|string|max:150',
            'usr_email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
            'usr_role' => [
                'required',
                Rule::in([
                    'faculty',
                    'department_chair',
                    'dean',
                    'system_admin',
                ]),
            ],
            'usr_bio' => 'nullable|string|max:2000',
        ]);

        User::create([
            'usr_name'          => $data['usr_name'],
            'usr_email'         => $data['usr_email'],
            'usr_password_hash' => Hash::make($data['password']),
            'usr_role'          => $data['usr_role'],
            'usr_is_active'     => true,
            'usr_bio'          => $data['usr_bio'] ?? null,
        ]);

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
            'usr_bio'      => $user->usr_bio,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'usr_name' => 'required|string|max:150',
            'usr_email' => 'required|email|max:255',
            'usr_role' => [
                'required',
                Rule::in([
                    'faculty',
                    'department_chair',
                    'dean',
                    'system_admin',
                ]),
            ],
            'usr_is_active' => 'required|boolean',
            'usr_bio' => 'nullable|string|max:2000',
        ]);

        $user->update([
            'usr_name'      => $data['usr_name'],
            'usr_email'     => $data['usr_email'],
            'usr_role'      => $data['usr_role'],
            'usr_is_active' => $data['usr_is_active'],
            'usr_bio' => $data['usr_bio'] ?? null,
        ]);

        return redirect()
            ->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();

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