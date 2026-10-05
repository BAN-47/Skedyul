<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room', function (Blueprint $table) {
            // Keep existing rooms intact; admins can assign their colleges in Room Management.
            $table->uuid('room_college_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('room', function (Blueprint $table) {
            $table->dropColumn('room_college_id');
        });
    }
};
