<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designations', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->string('name')->unique();
            $table->integer('day_cap')->default(30);
            $table->integer('evening_cap')->default(30);
            $table->integer('max_faculty_count')->nullable();
            $table->timestamps();
        });

        // Drop strict constraint to allow dynamic positions and caps
        DB::statement('ALTER TABLE public.faculty DROP CONSTRAINT IF EXISTS faculty_special_position_cap_check');

        $designations = [
            ['name' => 'Vice-President', 'day_cap' => 3, 'evening_cap' => 30],
            ['name' => 'University Director', 'day_cap' => 6, 'evening_cap' => 30],
            ['name' => 'Campus Director', 'day_cap' => 6, 'evening_cap' => 30],
            ['name' => 'UD Data Protection Officer', 'day_cap' => 6, 'evening_cap' => 30],
            ['name' => 'Assistant Campus Director', 'day_cap' => 9, 'evening_cap' => 30],
            ['name' => 'Dean of Instruction', 'day_cap' => 9, 'evening_cap' => 30],
            ['name' => 'College Dean', 'day_cap' => 9, 'evening_cap' => 30],
            ['name' => 'Associate College Dean', 'day_cap' => 12, 'evening_cap' => 30],
            ['name' => 'Department Chairperson', 'day_cap' => 16, 'evening_cap' => 30],
            ['name' => 'PERG Chair', 'day_cap' => 16, 'evening_cap' => 30],
            ['name' => 'Campus Secretary', 'day_cap' => 16, 'evening_cap' => 30],
            ['name' => 'College Secretary', 'day_cap' => 16, 'evening_cap' => 30],
            ['name' => 'Research Chair', 'day_cap' => 16, 'evening_cap' => 30],
            ['name' => 'MIS Officer', 'day_cap' => 30, 'evening_cap' => 30],
        ];

        foreach ($designations as $d) {
            DB::table('designations')->insert(array_merge($d, [
                'id' => DB::raw('gen_random_uuid()'),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('designations');
        // Note: Re-adding the constraint in down() might fail if users added custom designations.
    }
};
