<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacultyEducationController extends Controller
{
    public function edit(Request $request, string $facultyId)
    {
        abort_unless($request->hasValidSignature(), 403);
        $faculty = Faculty::where('fac_id', $facultyId)->whereHas('user', fn ($query) => $query->where('usr_is_active', true))->firstOrFail();

        return view('faculty.education', compact('faculty'));
    }

    public function update(Request $request, string $facultyId)
    {
        abort_unless($request->hasValidSignature(), 403);
        $faculty = Faculty::where('fac_id', $facultyId)
            ->whereHas('user', fn ($query) => $query->where('usr_is_active', true))
            ->firstOrFail();
        $isFullTime = $faculty->fac_employment_type === 'full_time';

        $data = $request->validate([
            'fac_bachelors_degree' => ['required', 'string', 'max:255'],
            'fac_bachelors_abbr' => ['required', 'string', 'max:40'],
            'fac_masters_degree' => [
                $isFullTime ? 'required' : 'nullable',
                'string',
                'max:255',
            ],
            'fac_masters_abbr' => [
                $isFullTime || $request->filled('fac_masters_degree') ? 'required' : 'nullable',
                'string',
                'max:40',
            ],
            'fac_doctorate_degree' => ['nullable', 'string', 'max:255'],
            'fac_doctorate_abbr' => [
                $request->filled('fac_doctorate_degree') ? 'required' : 'nullable',
                'string',
                'max:60',
            ],
        ]);

        $faculty->update($data);
        Auth::login($faculty->user);

        return redirect()->route('faculty.dashboard');
    }
}
