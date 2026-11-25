<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workload_overrides', function (Blueprint $table) {
            $table->uuid('wlo_id')->primary();
            $table->uuid('wlo_load_id')->unique();
            $table->uuid('wlo_fac_id');
            $table->uuid('wlo_course_id');
            $table->uuid('wlo_section_id')->nullable();
            $table->uuid('wlo_semester_id');
            $table->uuid('wlo_approved_by');
            $table->string('wlo_shift', 16);
            $table->string('wlo_designation')->nullable();
            $table->decimal('wlo_hours_before', 8, 2);
            $table->decimal('wlo_hours_added', 8, 2);
            $table->decimal('wlo_hours_after', 8, 2);
            $table->decimal('wlo_cap_hours', 8, 2);
            $table->text('wlo_reason');
            $table->timestamps();
            $table->index(['wlo_semester_id', 'wlo_fac_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workload_overrides');
    }
};
