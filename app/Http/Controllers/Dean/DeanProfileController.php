<?php

namespace App\Http\Controllers\Dean;

use App\Models\Dean;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class DeanProfileController extends Controller
{
    public function settings()
    {
        return view('dean.settings', [
            'dean' => $this->currentDean(),
        ]);
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

        if ($dean->dean_profile_image_public_id) {
            Cloudinary::uploadApi()->destroy($dean->dean_profile_image_public_id);
        }

        $result = Cloudinary::uploadApi()->upload($request->file('avatar')->getRealPath(), [
            'folder' => 'skedyul/dean-avatars',
            'public_id' => 'dean_' . $dean->dean_id,
            'overwrite' => true,
            'transformation' => [
                'width' => 300,
                'height' => 300,
                'crop' => 'fill',
                'gravity' => 'face',
            ],
        ]);

        $dean->update([
            'dean_profile_image' => $result['secure_url'],
            'dean_profile_image_public_id' => $result['public_id'],
        ]);

        return response()->json(['success' => true, 'url' => $result['secure_url']]);
    }

    public function removeAvatar()
    {
        $dean = $this->currentDean();

        if ($dean->dean_profile_image_public_id) {
            Cloudinary::uploadApi()->destroy($dean->dean_profile_image_public_id);
        }

        $dean->update([
            'dean_profile_image' => null,
            'dean_profile_image_public_id' => null,
        ]);

        return response()->json(['success' => true]);
    }

    public function updatePersonalInfo(Request $request)
    {
        $data = $request->validate([
            'dean_first_name' => 'required|string|max:100',
            'dean_last_name' => 'required|string|max:100',
            'dean_middle_name' => 'nullable|string|max:100',
            'dean_suffix' => 'nullable|string|max:20',
            'dean_employee_id' => 'nullable|string|max:50',
            'dean_gender' => 'nullable|string|max:30',
            'dean_civil_status' => 'nullable|string|max:30',
            'dean_dob' => 'nullable|date',
            'dean_nationality' => 'nullable|string|max:100',
        ]);

        $dean = $this->currentDean();
        $dean->update($data);

        return response()->json(['success' => true]);
    }

    public function updateContact(Request $request)
    {
        $data = $request->validate([
            'dean_gmail' => 'required|email|max:150',
            'dean_phone_number' => 'nullable|string|max:30',
            'dean_office_address' => 'nullable|string|max:255',
            'dean_bio' => 'nullable|string|max:1000',
        ]);

        $dean = $this->currentDean();
        $dean->update($data);

        return response()->json(['success' => true]);
    }

    
}