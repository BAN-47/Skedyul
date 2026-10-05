<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // A study load is the course assignment; schedule rows are its meeting
        // sessions. A course/section may meet on multiple days or times.
        DB::statement('ALTER TABLE schedule DROP CONSTRAINT IF EXISTS schedule_sch_load_id_key');
        DB::statement('ALTER TABLE schedule DROP CONSTRAINT IF EXISTS schedule_sch_load_id_unique');
    }

    public function down(): void
    {
        $hasRepeatedSessions = DB::table('schedule')
            ->select('sch_load_id')
            ->groupBy('sch_load_id')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($hasRepeatedSessions) {
            throw new RuntimeException(
                'Cannot restore the one-session-per-load constraint while repeated schedule sessions exist.'
            );
        }

        DB::statement('ALTER TABLE schedule ADD CONSTRAINT schedule_sch_load_id_key UNIQUE (sch_load_id)');
    }
};
