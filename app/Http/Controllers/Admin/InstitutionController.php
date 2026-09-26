<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InstitutionController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'inst_name' => 'required|string|max:255',
            'inst_branch_campus' => 'required|string|max:255',
            'inst_college' => 'required|string|max:255',
            'inst_abbreviation' => 'required|string|max:50',
            'inst_contact_email' => 'required|email|max:255',
            'inst_phone' => 'required|string|max:50',
        ]);

        // since there's only ever one institution row, update it if it exists, create it if not
        $institution = Institution::first();

        if ($institution) {
            $institution->update($validated);
        } else {
            $validated['inst_id'] = (string) Str::uuid();
            $institution = Institution::create($validated);
        }

        return response()->json(['success' => true]);
    }
}