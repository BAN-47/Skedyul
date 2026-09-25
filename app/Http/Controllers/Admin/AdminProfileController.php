<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = Auth::user();

        if ($user->profile_picture_public_id) {
            Storage::disk('cloudinary')->delete($user->profile_picture_public_id);
        }

        $path = $request->file('profile_picture')->store('skedyul/profile_pictures', 'cloudinary');

        $user->update([
            'profile_picture' => Storage::disk('cloudinary')->url($path),
            'profile_picture_public_id' => $path,
        ]);

        return response()->json([
            'success' => true,
            'url' => Storage::disk('cloudinary')->url($path),
        ]);
    }

    public function removeProfilePicture(Request $request)
    {
        $user = Auth::user();

        if ($user->profile_picture_public_id) {
            Storage::disk('cloudinary')->delete($user->profile_picture_public_id);
        }

        $user->update([
            'profile_picture' => null,
            'profile_picture_public_id' => null,
        ]);

        return response()->json(['success' => true]);
    }

    public function updatePersonalInfo(Request $request)
    {
        $validated = $request->validate([
            'usr_first_name' => 'required|string|max:100',
            'usr_last_name' => 'required|string|max:100',
            'usr_middle_name' => 'nullable|string|max:100',
            'usr_suffix' => 'nullable|string|max:10',
            'usr_rank_title' => 'nullable|string|max:150',
            'usr_employee_id' => 'nullable|string|max:50',
            'usr_gender' => 'nullable|string|max:50',
            'usr_civil_status' => 'nullable|string|max:50',
            'usr_dob' => 'nullable|date',
            'usr_nationality' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        $user->update($validated);

        return response()->json([
            'success' => true,
            'usr_name' => $user->usr_name,
        ]);
    }
}