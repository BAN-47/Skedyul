<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        $academicYears = DB::table('academic_year')->get();
        $existingYear = $academicYears->first(function ($row) use ($yearLabel) {
            return $this->normalizeYear((string) ($row->ay_academic_year ?? '')) === $yearLabel
                || $this->normalizeYear((string) ($row->ay_year_label ?? '')) === $yearLabel;
        });
        $isNewAcademicYear = ! $existingYear;
        $currentSemester = DB::table('semester')->where('sem_is_active', true)->first();
        $isConfiguringUpcomingSecond = ! $isNewAcademicYear
            && (bool) ($existingYear->ay_is_active ?? false)
            && $validated['sem_name'] === '2nd Semester'
            && $currentSemester
            && (string) $currentSemester->sem_ay_id === (string) $existingYear->ay_id
            && $currentSemester->sem_name === '1st Semester';

        if ($existingYear && !(bool) $existingYear->ay_is_active
            && DB::table('academic_year')->where('ay_is_active', true)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Use the Make Current action in the year review list to switch to an archived year without changing its saved semester dates.',
            ], 422);
        }

        if ($isNewAcademicYear && ! $request->boolean('confirm_new_academic_year')) {
            return response()->json([
                'success' => false,
                'confirmation_required' => true,
                'message' => 'Creating a new academic year keeps existing schedules in the archives, locks previous academic years for editing, turns Advance Plotting off, and starts the new year in 1st Semester. Continue?',
            ], 409);
        }

        if (! self::hasColumn('semester', 'sem_ay_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Academic year-specific semesters are not available in the database. No changes were made.',
            ], 422);
        }

        if (! $isNewAcademicYear && $validated['sem_name'] === '2nd Semester') {
            $firstSemester = DB::table('semester')
                ->where('sem_ay_id', $existingYear->ay_id)
                ->where('sem_name', '1st Semester')
                ->first();

            if ($firstSemester && $validated['sem_start_date'] <= $firstSemester->sem_end_date) {
                return response()->json([
                    'success' => false,
                    'message' => 'The 2nd Semester start date must be after the 1st Semester end date.',
                ], 422);
            }
        }

        if ($isNewAcademicYear && ! Schema::hasTable('schedule_archives')) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule Archives are not available yet. Apply the schedule archives database migration before creating a new academic year.',
            ], 422);
        }

        $semName = $isNewAcademicYear ? '1st Semester' : $validated['sem_name'];

        try {
            $result = DB::transaction(function () use ($validated, $yearLabel, $semName, $existingYear, $isNewAcademicYear, $isConfiguringUpcomingSecond) {
                $previousYear = DB::table('academic_year')->where('ay_is_active', true)->first();
                if ($isNewAcademicYear) {
                    // Fill any older archive gaps as well as snapshotting the
                    // currently active year. This never deletes live schedule rows.
                    DB::table('academic_year')->pluck('ay_id')
                        ->each(fn ($yearId) => $this->archiveAcademicYearSchedules((string) $yearId));
                }

                // 1) Academic year: find or create, set active
                if (! $isConfiguringUpcomingSecond) {
                    DB::table('academic_year')->update(['ay_is_active' => false]);
                }

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
                if (! $isConfiguringUpcomingSecond) {
                    DB::table('semester')->update(['sem_is_active' => false]);
                }
                if ($isNewAcademicYear && self::hasColumn('semester', 'sem_is_plotting_enabled')) {
                    DB::table('semester')->update(['sem_is_plotting_enabled' => false]);
                }

                $sem = DB::table('semester')
                    ->where('sem_ay_id', $ayId)
                    ->where('sem_name', $semName)
                    ->first();

                // Configuring a future 2nd Semester must not make it current;
                // Admin can enable Advance Plotting or switch it manually later.
                $plottingEnabled = ! $isConfiguringUpcomingSecond;

                if (! $sem) {
                    $semId = (string) Str::uuid();
                    $insert = [
                        'sem_id' => $semId,
                        'sem_name' => $semName,
                        'sem_ay_id' => $ayId,
                        'sem_start_date' => $validated['sem_start_date'],
                        'sem_end_date' => $validated['sem_end_date'],
                        'sem_is_active' => ! $isConfiguringUpcomingSecond,
                        'sem_is_plotting_enabled' => $isConfiguringUpcomingSecond
                            ? (bool) ($sem->sem_is_plotting_enabled ?? false)
                            : $plottingEnabled,
                    ];
                    DB::table('semester')->insert($insert);
                } else {
                    $update = [
                        'sem_start_date' => $validated['sem_start_date'],
                        'sem_end_date' => $validated['sem_end_date'],
                        'sem_is_active' => ! $isConfiguringUpcomingSecond,
                        'sem_is_plotting_enabled' => $isConfiguringUpcomingSecond
                            ? (bool) ($sem->sem_is_plotting_enabled ?? false)
                            : $plottingEnabled,
                    ];
                    DB::table('semester')->where('sem_id', $sem->sem_id)->update($update);
                }

                // A new academic year always has both term records. Use a
                // valid date range for the inactive 2nd-semester placeholder:
                // copying the 1st-semester range satisfies databases that
                // enforce end_date > start_date, while the overlap ensures
                // auto-advance/advance plotting cannot treat it as configured.
                if ($isNewAcademicYear) {
                    $second = DB::table('semester')
                        ->where('sem_ay_id', $ayId)
                        ->where('sem_name', '2nd Semester')
                        ->first();

                    if (! $second) {
                        DB::table('semester')->insert([
                            'sem_id' => (string) Str::uuid(),
                            'sem_name' => '2nd Semester',
                            'sem_ay_id' => $ayId,
                            'sem_start_date' => $validated['sem_start_date'],
                            'sem_end_date' => $validated['sem_end_date'],
                            'sem_is_active' => false,
                            'sem_is_plotting_enabled' => false,
                        ]);
                    }
                }

                return [
                    'label' => $yearLabel.' · '.$semName,
                    'configured_upcoming_semester' => $isConfiguringUpcomingSecond,
                    'archived_year' => $isNewAcademicYear ? ($previousYear?->ay_academic_year ?? null) : null,
                ];
            });
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Could not save the academic period. Please try again or contact an administrator.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => $result['configured_upcoming_semester']
                ? 'Dates saved for '.$result['label'].'. 1st Semester remains current; enable Advance Plotting or switch semesters when ready.'
                : 'Current period set to '.$result['label'].($result['archived_year'] ? '. Previous academic year schedules were archived and advance plotting was reset.' : ''),
            'label' => $result['label'].($result['configured_upcoming_semester'] ? ' (Dates saved)' : ' (Current)'),
        ]);
    }

    private function archiveAcademicYearSchedules(string $academicYearId): void
    {
        $semesterIds = DB::table('semester')
            ->where('sem_ay_id', $academicYearId)
            ->whereIn('sem_name', ['1st Semester', '2nd Semester'])
            ->pluck('sem_id');

        if ($semesterIds->isEmpty()) {
            return;
        }

        $schedules = DB::table('schedule as sch')
            ->whereIn('sch.sch_sem_id', $semesterIds)
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('schedule_archives as sa')
                    ->whereColumn('sa.sar_sch_id', 'sch.sch_id');
            })
            ->get();

        foreach ($schedules->chunk(500) as $chunk) {
            $now = now();
            DB::table('schedule_archives')->insert($chunk->map(fn ($schedule) => [
                'sar_id' => (string) Str::uuid(),
                'sar_sch_id' => $schedule->sch_id,
                'sar_sem_id' => $schedule->sch_sem_id,
                'sar_ay_id' => $academicYearId,
                'sar_fac_id' => $schedule->sch_fac_id,
                'sar_course_id' => $schedule->sch_course_id,
                'sar_sec_id' => $schedule->sch_sec_id,
                'sar_room_id' => $schedule->sch_room_id,
                'sar_day' => $schedule->sch_day,
                'sar_start_time' => $schedule->sch_start_time,
                'sar_end_time' => $schedule->sch_end_time,
                'sar_shift' => $schedule->sch_shift,
                'sar_status' => $schedule->sch_status,
                'sar_is_active' => $schedule->sch_is_active,
                'sar_description' => null,
                'sar_created_by' => $schedule->sch_created_by,
                'sar_updated_by' => null,
                'sar_archived_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all());
        }
    }

    /** Make a saved semester current again for review or controlled testing. */
    public function activateArchivedSemester(Request $request, string $id)
    {
        $target = DB::table('semester as s')
            ->join('academic_year as ay', 'ay.ay_id', '=', 's.sem_ay_id')
            ->where('s.sem_id', $id)
            ->first([
                's.sem_id', 's.sem_name', 's.sem_ay_id', 's.sem_start_date', 's.sem_end_date',
                'ay.ay_academic_year', 'ay.ay_year_label', 'ay.ay_is_active',
            ]);

        if (! $target) {
            return response()->json(['success' => false, 'message' => 'Academic semester not found.'], 404);
        }

        $isArchivedYear = ! (bool) $target->ay_is_active;
        if (! $isArchivedYear && (empty($target->sem_start_date) || empty($target->sem_end_date)
            || $target->sem_end_date <= $target->sem_start_date)) {
            return response()->json([
                'success' => false,
                'message' => 'This semester has incomplete or invalid dates. Set valid dates before making it current.',
            ], 422);
        }

        if (! $isArchivedYear && $target->sem_name === '2nd Semester') {
            $firstSemesterEnd = DB::table('semester')
                ->where('sem_ay_id', $target->sem_ay_id)
                ->where('sem_name', '1st Semester')
                ->value('sem_end_date');
            if ($firstSemesterEnd && $target->sem_start_date <= $firstSemesterEnd) {
                return response()->json([
                    'success' => false,
                    'message' => 'Set the 2nd Semester dates so they start after the 1st Semester ends before switching.',
                ], 422);
            }
        }

        if (! Schema::hasTable('schedule_archives')) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule Archives are unavailable. No academic year was switched.',
            ], 422);
        }

        try {
            DB::transaction(function () use ($target) {
                $currentYear = DB::table('academic_year')->where('ay_is_active', true)->first();
                if ($currentYear && (string) $currentYear->ay_id !== (string) $target->sem_ay_id) {
                    // Snapshot any newly added schedules before leaving this year.
                    $this->archiveAcademicYearSchedules((string) $currentYear->ay_id);
                }

                DB::table('academic_year')->update(['ay_is_active' => false]);
                DB::table('academic_year')->where('ay_id', $target->sem_ay_id)->update(['ay_is_active' => true]);
                DB::table('semester')->update(['sem_is_active' => false]);
                DB::table('semester')->where('sem_id', $target->sem_id)->update(['sem_is_active' => true]);
                if (self::hasColumn('semester', 'sem_is_plotting_enabled')) {
                    DB::table('semester')->update(['sem_is_plotting_enabled' => false]);
                }
            });
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Could not switch the current academic year. Existing schedules were not intentionally changed.',
            ], 500);
        }

        $yearLabel = $target->ay_academic_year ?? $target->ay_year_label ?? 'Selected year';

        return response()->json([
            'success' => true,
            'message' => $yearLabel.' · '.$target->sem_name.' is now current. Other years remain saved and view-only.',
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

            // Older years can be intentionally reactivated by Admin for review/testing.
            // Do not let their historical dates auto-advance the selected period.
            $latestYear = DB::table('academic_year')->orderByDesc('ay_academic_year')->first();
            $activeYear = DB::table('academic_year')->where('ay_is_active', true)->first();
            if ($latestYear && $activeYear && (string) $latestYear->ay_id !== (string) $activeYear->ay_id) {
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
                    if (empty($second->sem_start_date)
                        || empty($second->sem_end_date)
                        || $second->sem_start_date <= $active->sem_end_date
                        || $second->sem_end_date <= $second->sem_start_date) {
                        return 'Set valid 2nd Semester dates in Admin Settings before switching semesters.';
                    }

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
                    if (empty($second->sem_start_date)
                        || empty($second->sem_end_date)
                        || $second->sem_start_date <= $active->sem_end_date
                        || $second->sem_end_date <= $second->sem_start_date) {
                        return 'Set valid 2nd Semester dates in Admin Settings before switching semesters.';
                    }

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

            return 'The automatic semester switch failed. Please check the application logs.';
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

        $activeYear = DB::table('academic_year')->where('ay_is_active', true)->first();
        $activeSemester = DB::table('semester')->where('sem_is_active', true)->first();
        $firstSemesterEnd = DB::table('semester')
            ->where('sem_ay_id', $semester->sem_ay_id)
            ->where('sem_name', '1st Semester')
            ->value('sem_end_date');
        $isValidAdvanceTarget = !$activeYear
            || !$activeSemester
            ? false
            : ((string) $semester->sem_ay_id === (string) $activeYear->ay_id
                && (string) $activeSemester->sem_ay_id === (string) $activeYear->ay_id
                && $activeSemester->sem_name === '1st Semester'
                && $semester->sem_name === '2nd Semester'
                && $firstSemesterEnd
                && $semester->sem_start_date > $firstSemesterEnd
                && $semester->sem_end_date > $semester->sem_start_date
                && !(bool) $semester->sem_is_active);

        // Permit disabling stale legacy flags (for example, Advance Plotting
        // incorrectly left on for 1st Semester), but only allow enabling the
        // current academic year's future 2nd Semester while 1st is current.
        if (! $semester->sem_is_plotting_enabled && ! $isValidAdvanceTarget) {
            return response()->json([
                'success' => false,
                'message' => 'Advance Plotting can only be enabled for the current academic year’s upcoming 2nd Semester while 1st Semester is current.',
            ], 422);
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
