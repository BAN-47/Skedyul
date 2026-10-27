<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('room', 'room_college_id')) {
            Schema::table('room', function (Blueprint $table) {
                $table->uuid('room_college_id')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('room', function (Blueprint $table) {
            $table->dropColumn('room_college_id');
        });
    }
};
