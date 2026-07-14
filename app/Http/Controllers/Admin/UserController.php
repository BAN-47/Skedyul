<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('usr_name')->get();

        return view('admin.user_accounts', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = [
            'usr_name' => $request->input('usr_name'),
            'usr_email' => $request->input('usr_email'),
            'usr_password_hash' => Hash::make($request->input('password')),
            'usr_role' => $request->input('usr_role'),
            'usr_is_active' => $request->boolean('usr_is_active', true),
        ];

        User::create($data);

        return redirect()
            ->route('admin.users')
            ->with('success', 'User created successfully');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'usr_name' => $request->input('usr_name'),
            'usr_email' => $request->input('usr_email'),
            'usr_role' => $request->input('usr_role'),
            'usr_is_active' => $request->boolean('usr_is_active', $user->usr_is_active),
        ]);

        return redirect()
            ->route('admin.users')
            ->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()
            ->route('admin.users')
            ->with('success', 'User deleted successfully');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->usr_is_active = !$user->usr_is_active;
        $user->save();

        return back();
    }
}