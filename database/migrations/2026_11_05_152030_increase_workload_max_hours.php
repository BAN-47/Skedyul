<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing check constraint and recreate with higher limit
        DB::statement('ALTER TABLE workload DROP CONSTRAINT IF EXISTS workload_wl_total_hours_check');
        DB::statement('ALTER TABLE workload ADD CONSTRAINT workload_wl_total_hours_check CHECK (wl_total_hours >= 0 AND wl_total_hours <= 36)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE workload DROP CONSTRAINT IF EXISTS workload_wl_total_hours_check');
        DB::statement('ALTER TABLE workload ADD CONSTRAINT workload_wl_total_hours_check CHECK (wl_total_hours >= 0 AND wl_total_hours <= 30)');
    }
};