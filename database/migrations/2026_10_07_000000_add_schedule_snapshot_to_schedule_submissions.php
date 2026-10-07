<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_submission', function (Blueprint $table) {
            $table->json('schsub_schedule_snapshot')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('schedule_submission', function (Blueprint $table) {
            $table->dropColumn('schsub_schedule_snapshot');
        });
    }
};
