<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('USER', function (Blueprint $table): void {
            $table->timestamp('usr_last_login_at')->useCurrent();
        });

        DB::table('USER')
            ->whereNull('usr_last_login_at')
            ->update(['usr_last_login_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('USER', function (Blueprint $table): void {
            $table->dropColumn('usr_last_login_at');
        });
    }
};
