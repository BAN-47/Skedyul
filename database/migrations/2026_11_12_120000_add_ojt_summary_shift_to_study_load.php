<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('study_load', 'sl_ojt_is_evening')) {
            Schema::table('study_load', function (Blueprint $table) {
                $table->boolean('sl_ojt_is_evening')->default(false);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('study_load', 'sl_ojt_is_evening')) {
            Schema::table('study_load', function (Blueprint $table) {
                $table->dropColumn('sl_ojt_is_evening');
            });
        }
    }
};
