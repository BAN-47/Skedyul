<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_archives', function (Blueprint $table) {
            $table->uuid('sar_id')->primary();
            $table->uuid('sar_sch_id')->nullable();
            $table->uuid('sar_sem_id')->nullable();
            $table->uuid('sar_ay_id')->nullable();
            $table->uuid('sar_fac_id')->nullable();
            $table->uuid('sar_course_id')->nullable();
            $table->uuid('sar_sec_id')->nullable();
            $table->uuid('sar_room_id')->nullable();
            $table->string('sar_day')->nullable();
            $table->time('sar_start_time')->nullable();
            $table->time('sar_end_time')->nullable();
            $table->string('sar_shift')->nullable();
            $table->string('sar_status')->nullable();
            $table->boolean('sar_is_active')->default(false);
            $table->text('sar_description')->nullable();
            $table->uuid('sar_created_by')->nullable();
            $table->uuid('sar_updated_by')->nullable();
            $table->timestamp('sar_archived_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_archives');
    }
};