<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('USER', function (Blueprint $table) {
            $table->integer('usr_failed_login_attempts')->default(0);
            $table->timestamp('usr_locked_until')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('USER', function (Blueprint $table) {
            $table->dropColumn(['usr_failed_login_attempts', 'usr_locked_until']);
        });
    }
};
