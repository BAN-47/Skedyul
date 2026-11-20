<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('faculty', 'fac_is_placeholder')) {
            DB::statement('ALTER TABLE public.faculty ADD COLUMN fac_is_placeholder BOOLEAN NOT NULL DEFAULT FALSE');
        }

        // This is the Professor X faculty record the project owner identified.
        $placeholderUpdated = DB::table('faculty')
            ->where('fac_id', '01a1e95e-aa6f-79eb-ba98-48d3d1533d3f')
            ->update(['fac_is_placeholder' => true]);
        if (!$placeholderUpdated) {
            throw new RuntimeException('Professor X faculty record was not found. Confirm the configured faculty ID before migrating.');
        }

        // Pending-placeholder schedules do not create study_load rows.
        DB::statement('ALTER TABLE public.schedule ALTER COLUMN sch_load_id DROP NOT NULL');
        if (!Schema::hasColumn('schedule', 'sch_fac_assignment_dept_id')) {
            DB::statement('ALTER TABLE public.schedule ADD COLUMN sch_fac_assignment_dept_id UUID NULL REFERENCES public.department(dept_id) ON DELETE SET NULL');
        }
    }

    public function down(): void
    {
        if (DB::table('schedule')->where('sch_fac_id', '01a1e95e-aa6f-79eb-ba98-48d3d1533d3f')->exists()) {
            throw new RuntimeException('Assign Professor X schedules to real faculty before removing the unassigned-faculty feature.');
        }

        DB::table('faculty')->where('fac_id', '01a1e95e-aa6f-79eb-ba98-48d3d1533d3f')
            ->update(['fac_is_placeholder' => false]);

        DB::statement('ALTER TABLE public.schedule DROP COLUMN IF EXISTS sch_fac_assignment_dept_id');
        DB::statement('ALTER TABLE public.faculty DROP COLUMN IF EXISTS fac_is_placeholder');
    }
};
