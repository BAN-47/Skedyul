<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Admin Settings — Academic period
 *
 * academic_year = many years (2026-2027, 2027-2028, ...)
 * semester      = ONLY two rows: "1st Semester" and "2nd Semester"
 *
 * Active period = active academic year + active semester (labels joined in UI).
 */
class AcademicYearController extends Controller
{
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'ay_academic_year' => 'required|string|max:30',
                'sem_name'         => 'required|string|in:1st Semester,2nd Semester',
                'sem_start_date'   => 'required|date',
                'sem_end_date'     => 'required|date|after_or_equal:sem_start_date',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?? 'Invalid input.',
            ], 422);
        }

        $yearLabel = $this->normalizeYear($validated['ay_academic_year']);
        $semName   = $validated['sem_name'];

        try {
            $result = DB::transaction(function () use ($validated, $yearLabel, $semName) {
                // 1) Academic year: find or create, set active
                DB::table('academic_year')->update(['ay_is_active' => false]);

                $ay = DB::table('academic_year')->get()->first(function ($row) use ($yearLabel) {
                    return $this->normalizeYear((string) ($row->ay_academic_year ?? '')) === $yearLabel
                        || $this->normalizeYear((string) ($row->ay_year_label ?? '')) === $yearLabel;
                });

                $ayId = $ay->ay_id ?? (string) Str::uuid();
                $ayPayload = [
                    'ay_academic_year' => $yearLabel,
                    'ay_is_active'     => true,
                ];
                if ($this->hasColumn('academic_year', 'ay_year_label')) {
                    $ayPayload['ay_year_label'] = $yearLabel;
                }

                if ($ay) {
                    DB::table('academic_year')->where('ay_id', $ayId)->update($ayPayload);
                } else {
                    $ayPayload['ay_id'] = $ayId;
                    if ($this->hasColumn('academic_year', 'ay_created_at')) {
                        $ayPayload['ay_created_at'] = now();
                    }
                    DB::table('academic_year')->insert($ayPayload);
                }

                // 2) Semester: only update the matching global 1st/2nd row
                DB::table('semester')->update(['sem_is_active' => false]);

                $sem = DB::table('semester')
                    ->where('sem_name', $semName)
                    ->first();

                if (!$sem) {
                    // Create the missing template row if seed was incomplete
                    $semId = (string) Str::uuid();
                    $insert = [
                        'sem_id'         => $semId,
                        'sem_name'       => $semName,
                        'sem_start_date' => $validated['sem_start_date'],
                        'sem_end_date'   => $validated['sem_end_date'],
                        'sem_is_active'  => true,
                    ];
                    if ($this->hasColumn('semester', 'sem_ay_id')) {
                        $insert['sem_ay_id'] = $ayId; // optional link to active year
                    }
                    DB::table('semester')->insert($insert);
                } else {
                    $update = [
                        'sem_start_date' => $validated['sem_start_date'],
                        'sem_end_date'   => $validated['sem_end_date'],
                        'sem_is_active'  => true,
                    ];
                    if ($this->hasColumn('semester', 'sem_ay_id')) {
                        $update['sem_ay_id'] = $ayId;
                    }
                    DB::table('semester')->where('sem_id', $sem->sem_id)->update($update);
                }

                return [
                    'label' => $yearLabel . ' · ' . $semName,
                ];
            });
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Could not save: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Current period set to ' . $result['label'],
            'label'   => $result['label'] . ' (Current)',
        ]);
    }

    /**
     * Auto-advance 1st → 2nd when end date passed.
     * When 2nd ends, leave inactive and return a notice for admin.
     * Call from settings page / PBS / PBT bootstrap.
     */
    public static function autoAdvanceIfNeeded(): ?string
    {
        try {
            $active = DB::table('semester')->where('sem_is_active', true)->first();
            if (!$active || empty($active->sem_end_date)) {
                return null;
            }

            $today = now()->startOfDay();
            $end   = \Illuminate\Support\Carbon::parse($active->sem_end_date)->startOfDay();

            if ($today->lte($end)) {
                return null; // still in range
            }

            // Past end date
            if (str_contains(strtolower($active->sem_name), '1st') || str_contains(strtolower($active->sem_name), 'first')) {
                DB::table('semester')->update(['sem_is_active' => false]);
                $second = DB::table('semester')->where('sem_name', '2nd Semester')->first();
                if ($second) {
                    DB::table('semester')->where('sem_id', $second->sem_id)->update(['sem_is_active' => true]);
                    return 'Automatically switched to 2nd Semester (1st Semester end date has passed).';
                }
            }

            // 2nd semester (or unknown) ended
            DB::table('semester')->update(['sem_is_active' => false]);

            // Best-effort admin notification
            try {
                if (class_exists(Notification::class) && DB::getSchemaBuilder()->hasTable('notification')) {
                    // soft notify — ignore if schema differs
                }
            } catch (\Throwable $e) {
                // ignore
            }

            return '2nd Semester has ended. Please set the next academic year and semester in Settings.';
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    private function hasColumn(string $table, string $column): bool
    {
        try {
            return DB::getSchemaBuilder()->hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function normalizeYear(string $value): string
    {
        $value = trim($value);
        $value = str_replace(['–', '—', '−'], '-', $value);
        $value = preg_replace('/\s*-\s*/', '-', $value);

        return $value;
    }
}
