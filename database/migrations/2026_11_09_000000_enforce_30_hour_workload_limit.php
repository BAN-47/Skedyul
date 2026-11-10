<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('workload')->where('wl_total_hours', '>', 60)->exists()) {
            throw new RuntimeException(
                'Cannot apply the separate-shift workload limit while workload records exceed 60 combined hours. Review those records before retrying.'
            );
        }

        DB::statement('ALTER TABLE workload DROP CONSTRAINT IF EXISTS workload_wl_total_hours_check');
        DB::statement('ALTER TABLE workload ADD CONSTRAINT workload_wl_total_hours_check CHECK (wl_total_hours >= 0 AND wl_total_hours <= 60)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE workload DROP CONSTRAINT IF EXISTS workload_wl_total_hours_check');
        DB::statement('ALTER TABLE workload ADD CONSTRAINT workload_wl_total_hours_check CHECK (wl_total_hours >= 0 AND wl_total_hours <= 36)');
    }
};
