<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Admin Settings → Current Academic Year
 *
 * Saving here is what PBS / PBT / Faculty Load use as "(Current)".
 * Only one academic year and one semester should be active at a time.
 */
class AcademicYearController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'ay_academic_year' => 'required|string|max:20',
            'sem_name'         => 'required|string|max:50',
            'sem_start_date'   => 'required|date',
            'sem_end_date'     => 'required|date|after:sem_start_date',
        ]);

        // Normalize "2026–2027" (en-dash) → "2026-2027"
        $yearLabel = $this->normalizeYear($validated['ay_academic_year']);
        $semName   = $this->normalizeSemesterName($validated['sem_name']);

        try {
            $result = DB::transaction(function () use ($validated, $yearLabel, $semName) {
                // 1) Clear every active flag
                AcademicYear::query()->update(['ay_is_active' => false]);
                Semester::query()->update(['sem_is_active' => false]);

                // 2) Find or create academic year (match normalized label)
                $academicYear = AcademicYear::query()
                    ->where('ay_academic_year', $yearLabel)
                    ->orWhere('ay_year_label', $yearLabel)
                    ->first();

                if (!$academicYear) {
                    // Also try loose match after normalizing existing rows
                    $academicYear = AcademicYear::all()->first(function ($ay) use ($yearLabel) {
                        return $this->normalizeYear($ay->ay_academic_year ?? '') === $yearLabel
                            || $this->normalizeYear($ay->ay_year_label ?? '') === $yearLabel;
                    });
                }

                if (!$academicYear) {
                    $academicYear = new AcademicYear();
                    $academicYear->ay_id = (string) Str::uuid();
                }

                $academicYear->ay_academic_year = $yearLabel;
                $academicYear->ay_year_label    = $yearLabel;
                $academicYear->ay_is_active     = true;
                $academicYear->save();

                // 3) Find semester under this AY (flexible name match)
                $semester = Semester::where('sem_ay_id', $academicYear->ay_id)
                    ->get()
                    ->first(function ($s) use ($semName) {
                        return $this->normalizeSemesterName($s->sem_name) === $semName;
                    });

                if (!$semester) {
                    $semester = new Semester();
                    $semester->sem_id = (string) Str::uuid();
                    $semester->sem_ay_id = $academicYear->ay_id;
                }

                // Store a consistent display name
                $semester->sem_name       = $semName;
                $semester->sem_start_date = $validated['sem_start_date'];
                $semester->sem_end_date   = $validated['sem_end_date'];
                $semester->sem_is_active  = true;
                $semester->save();

                return [
                    'ay'  => $academicYear->ay_academic_year,
                    'sem' => $semester->sem_name,
                ];
            });
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not save academic year: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Current period set to {$result['ay']} · {$result['sem']}",
            'label'   => "{$result['ay']} · {$result['sem']} (Current)",
        ]);
    }

    /** "2026–2027" / "2026 - 2027" → "2026-2027" */
    private function normalizeYear(string $value): string
    {
        $value = trim($value);
        $value = str_replace(['–', '—', '−'], '-', $value); // en/em/minus dashes
        $value = preg_replace('/\s*-\s*/', '-', $value);

        return $value;
    }

    /**
     * Normalize semester labels so "1st Semester", "First Semester", "1st Sem"
     * all map to the same canonical name.
     */
    private function normalizeSemesterName(string $value): string
    {
        $v = strtolower(trim($value));
        $v = preg_replace('/\s+/', ' ', $v);

        if (str_contains($v, 'summer')) {
            return 'Summer';
        }
        if (preg_match('/\b(1st|first|1)\b/', $v)) {
            return '1st Semester';
        }
        if (preg_match('/\b(2nd|second|2)\b/', $v)) {
            return '2nd Semester';
        }

        // Fallback: title-case whatever was typed
        return trim($value);
    }
}
