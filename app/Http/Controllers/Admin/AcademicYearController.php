<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AcademicYearController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'ay_academic_year' => 'required|string|max:20',
            'sem_name' => 'required|string|max:50',
            'sem_start_date' => 'required|date',
            'sem_end_date' => 'required|date|after:sem_start_date',
        ]);

        // deactivate any previously active academic year + semester
        AcademicYear::where('ay_is_active', true)->update(['ay_is_active' => false]);
        Semester::where('sem_is_active', true)->update(['sem_is_active' => false]);

        // find or create this academic year, mark it active
        $academicYear = AcademicYear::firstOrNew(['ay_academic_year' => $validated['ay_academic_year']]);
        if (!$academicYear->exists) {
            $academicYear->ay_id = (string) Str::uuid();
            $academicYear->ay_year_label = $validated['ay_academic_year'];
        }
        $academicYear->ay_is_active = true;
        $academicYear->save();

        // find or create the matching semester under this academic year, mark it active
        $semester = Semester::firstOrNew([
            'sem_ay_id' => $academicYear->ay_id,
            'sem_name' => $validated['sem_name'],
        ]);
        if (!$semester->exists) {
            $semester->sem_id = (string) Str::uuid();
        }
        $semester->sem_start_date = $validated['sem_start_date'];
        $semester->sem_end_date = $validated['sem_end_date'];
        $semester->sem_is_active = true;
        $semester->save();

        return response()->json(['success' => true]);
    }
}