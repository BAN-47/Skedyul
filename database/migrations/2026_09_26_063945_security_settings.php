<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('USER', function (Blueprint $table) {
            $table->integer('usr_session_timeout_minutes')->default(30);
            $table->integer('usr_max_login_attempts')->default(5);
        });
    }

    public function down(): void
    {
        Schema::table('USER', function (Blueprint $table) {
            $table->dropColumn(['usr_session_timeout_minutes', 'usr_max_login_attempts']);
        });
    }
};
