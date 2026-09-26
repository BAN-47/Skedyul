<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('USER', function (Blueprint $table) {
            $table->boolean('notif_new_user_registration')->default(true);
            $table->boolean('notif_faculty_overload')->default(true);
            $table->boolean('notif_system_backups')->default(false);
            $table->boolean('notif_login_activity')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('USER', function (Blueprint $table) {
            $table->dropColumn([
                'notif_new_user_registration',
                'notif_faculty_overload',
                'notif_system_backups',
                'notif_login_activity',
            ]);
        });
    }
};
