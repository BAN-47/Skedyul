<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Make sch_sec_id and sch_room_id nullable in schedule table
        Schema::table('schedule', function (Blueprint $table) {
            $table->uuid('sch_sec_id')->nullable()->change();
            $table->uuid('sch_room_id')->nullable()->change();
        });

        // 2. Make sl_sec_id nullable in study_load table
        Schema::table('study_load', function (Blueprint $table) {
            $table->uuid('sl_sec_id')->nullable()->change();
        });

        // 3. Insert REC subject into course table (using first department as default)
        // This assumes the course table has been created and has the required columns
        $departmentId = DB::table('department')->orderBy('dept_id')->value('dept_id');
        if (!DB::table('course')->where('course_dept_id', $departmentId)->where('course_code', 'REC')->exists()) {
            DB::table('course')->insert([
                'course_id' => DB::raw('gen_random_uuid()'),
                'course_college_id' => DB::table('college')->orderBy('college_id')->value('college_id'),
                'course_dept_id' => $departmentId,
                'course_code' => 'REC',
                'course_name' => 'Research, Extension and Consultation Time',
                'course_lecture_hours' => 0.0,
                'course_lab_hours' => 0.0,
                'course_year_level' => null,
                'course_semester' => 1,
                'course_is_active' => true,
                'course_created_at' => now(),
                'course_updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Remove REC subject
        DB::table('course')->where('course_code', 'REC')->delete();

        // Restore NOT NULL constraints (may fail if null values exist)
        Schema::table('schedule', function (Blueprint $table) {
            $table->uuid('sch_sec_id')->nullable(false)->change();
            $table->uuid('sch_room_id')->nullable(false)->change();
        });

        Schema::table('study_load', function (Blueprint $table) {
            $table->uuid('sl_sec_id')->nullable(false)->change();
        });
    }
};