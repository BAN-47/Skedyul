<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE public.faculty DROP CONSTRAINT IF EXISTS faculty_special_position_cap_check');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE public.faculty DROP CONSTRAINT IF EXISTS faculty_special_position_cap_check');
    }
};
