<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Admin Settings — Academic period
 *
 * academic_year = many years (2026-2027, 2027-2028, ...)
 * semester      = year-specific "1st Semester" and "2nd Semester" records
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
                'sem_name' => 'required|string|in:1st Semester,2nd Semester',
                'sem_start_date' => 'required|date',
                'sem_end_date' => 'required|date|after:sem_start_date',
                'sem_is_plotting_enabled' => 'sometimes|boolean',
                'confirm_new_academic_year' => 'sometimes|boolean',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?? 'Invalid input.',
            ], 422);
        }

        $yearLabel = $this->normalizeYear($validated['ay_academic_year']);
        $semName = $validated['sem_name'];
        $academicYears = DB::table('academic_year')->get();
        $existingYear = $academicYears->first(function ($row) use ($yearLabel) {
            return $this->normalizeYear((string) ($row->ay_academic_year ?? '')) === $yearLabel
                || $this->normalizeYear((string) ($row->ay_year_label ?? '')) === $yearLabel;
        });

        if (! $existingYear && ! $request->boolean('confirm_new_academic_year')) {
            return response()->json([
                'success' => false,
                'confirmation_required' => true,
                'message' => 'You are starting a new academic year. Previous schedules will not be deleted. The chair schedule view will switch to the new year. Continue?',
            ], 409);
        }

        if (! self::hasColumn('semester', 'sem_ay_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Academic year-specific semesters are not available in the database. No changes were made.',
            ], 422);
        }

        try {
            $result = DB::transaction(function () use ($validated, $yearLabel, $semName, $existingYear) {
                // 1) Academic year: find or create, set active
                DB::table('academic_year')->update(['ay_is_active' => false]);

                $ayId = $existingYear->ay_id ?? (string) Str::uuid();
                $ayPayload = [
                    'ay_academic_year' => $yearLabel,
                    'ay_is_active' => true,
                ];
                if (self::hasColumn('academic_year', 'ay_year_label')) {
                    $ayPayload['ay_year_label'] = $yearLabel;
                }

                if ($existingYear) {
                    DB::table('academic_year')->where('ay_id', $ayId)->update($ayPayload);
                } else {
                    $ayPayload['ay_id'] = $ayId;
                    if (self::hasColumn('academic_year', 'ay_created_at')) {
                        $ayPayload['ay_created_at'] = now();
                    }
                    DB::table('academic_year')->insert($ayPayload);
                }

                // Only update a semester belonging to this academic year.
                DB::table('semester')->update(['sem_is_active' => false]);

                $sem = DB::table('semester')
                    ->where('sem_ay_id', $ayId)
                    ->where('sem_name', $semName)
                    ->first();

                // Always enable plotting for active semesters
                $plottingEnabled = true;

                if (! $sem) {
                    $semId = (string) Str::uuid();
                    $insert = [
                        'sem_id' => $semId,
                        'sem_name' => $semName,
                        'sem_ay_id' => $ayId,
                        'sem_start_date' => $validated['sem_start_date'],
                        'sem_end_date' => $validated['sem_end_date'],
                        'sem_is_active' => true,
                        'sem_is_plotting_enabled' => $plottingEnabled,
                    ];
                    DB::table('semester')->insert($insert);
                } else {
                    $update = [
                        'sem_start_date' => $validated['sem_start_date'],
                        'sem_end_date' => $validated['sem_end_date'],
                        'sem_is_active' => true,
                        'sem_is_plotting_enabled' => $plottingEnabled,
                    ];
                    DB::table('semester')->where('sem_id', $sem->sem_id)->update($update);
                }

                return [
                    'label' => $yearLabel.' · '.$semName,
                ];
            });
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Could not save: '.$e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Current period set to '.$result['label'],
            'label' => $result['label'].' (Current)',
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
            if (! $active || empty($active->sem_end_date)) {
                return null;
            }

            $today = now()->startOfDay();
            $end = \Illuminate\Support\Carbon::parse($active->sem_end_date)->startOfDay();

            if ($today->lte($end)) {
                return null; // still in range
            }

            // Past end date
            if (str_contains(strtolower($active->sem_name), '1st') || str_contains(strtolower($active->sem_name), 'first')) {
                $secondQuery = DB::table('semester')->where('sem_name', '2nd Semester');
                if (! empty($active->sem_ay_id) && self::hasColumn('semester', 'sem_ay_id')) {
                    $secondQuery->where('sem_ay_id', $active->sem_ay_id);
                }
                $second = $secondQuery->first();
                if ($second) {
                    DB::table('semester')->update(['sem_is_active' => false]);
                    DB::table('semester')->where('sem_id', $second->sem_id)->update(['sem_is_active' => true]);

                    return 'Automatically switched to 2nd Semester (1st Semester end date has passed).';
                }

                return 'No 2nd Semester is configured for the active academic year.';
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

    private static function hasColumn(string $table, string $column): bool
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

    /**
     * Switch the official semester if its end date passes without changing schedules.
     * If enabled in settings:
     *  - 1st → 2nd when 1st end date passes
     *  - 2nd → deactivate the official period and leave admin to set next AY
     */
    public function autoSwitchSemester(): ?string
    {
        $enabled = (bool) SystemSetting::get('auto_switch_semester', true);
        if (! $enabled) {
            return null;
        }

        try {
            $active = DB::table('semester')->where('sem_is_active', true)->first();
            if (! $active || empty($active->sem_end_date)) {
                return null;
            }

            $today = Carbon::now()->startOfDay();
            $end = Carbon::parse($active->sem_end_date)->startOfDay();

            if ($today->lte($end)) {
                return null;
            }

            // Past end date
            if (str_contains(strtolower($active->sem_name), '1st') || str_contains(strtolower($active->sem_name), 'first')) {
                // 1st → 2nd
                $secondQuery = DB::table('semester')->where('sem_name', '2nd Semester');
                if (! empty($active->sem_ay_id) && self::hasColumn('semester', 'sem_ay_id')) {
                    $secondQuery->where('sem_ay_id', $active->sem_ay_id);
                }
                $second = $secondQuery->first();
                if ($second) {
                    DB::table('semester')->update(['sem_is_active' => false]);
                    DB::table('semester')->where('sem_id', $second->sem_id)->update(['sem_is_active' => true]);

                    return 'Auto-switched to 2nd Semester.';
                }

                return '2nd Semester not found.';
            }

            // End the official period without changing its schedule or workload records.
            DB::table('semester')->update(['sem_is_active' => false]);

            return '2nd Semester ended. Existing schedules were preserved. Please set the next academic year in Settings.';
        } catch (\Throwable $e) {
            report($e);

            return 'Auto-switch failed: '.$e->getMessage();
        }
    }

    public function togglePlotting(Request $request, $id)
    {
        $semester = DB::table('semester')->where('sem_id', $id)->first();
        
        if (!$semester) {
            return response()->json([
                'success' => false,
                'message' => 'Semester not found.'
            ], 404);
        }

        $newValue = !$semester->sem_is_plotting_enabled;
        
        DB::table('semester')->where('sem_id', $id)->update([
            'sem_is_plotting_enabled' => $newValue,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Advance plotting status updated.',
            'enabled' => $newValue,
        ]);
    }
}
