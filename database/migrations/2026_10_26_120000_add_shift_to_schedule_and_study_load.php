<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('schedule', 'sch_shift')) {
            Schema::table('schedule', function (Blueprint $table) {
                $table->string('sch_shift', 10)->default('day');
            });
        }

        if (!Schema::hasColumn('study_load', 'sl_shift')) {
            Schema::table('study_load', function (Blueprint $table) {
                $table->string('sl_shift', 10)->default('day');
            });
        }

        // Preserve the existing timetable's Day/Evening classification.
        DB::table('schedule')
            ->whereRaw("sch_start_time::time >= TIME '16:00'")
            ->update(['sch_shift' => 'evening']);

        // Keep each existing faculty/course/section load in the shift where
        // its first scheduled meeting occurs. Unscheduled loads remain Day.
        DB::statement(<<<'SQL'
            UPDATE study_load AS sl
            SET sl_shift = picked.sch_shift
            FROM (
                SELECT DISTINCT ON (sch_load_id) sch_load_id, sch_shift
                FROM schedule
                WHERE sch_load_id IS NOT NULL AND sch_is_active IS TRUE
                ORDER BY sch_load_id, sch_start_time
            ) AS picked
            WHERE picked.sch_load_id = sl.sl_id
        SQL);
    }

    public function down(): void
    {
        foreach (['schedule' => 'sch_shift', 'study_load' => 'sl_shift'] as $tableName => $column) {
            if (Schema::hasColumn($tableName, $column)) {
                Schema::table($tableName, function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
