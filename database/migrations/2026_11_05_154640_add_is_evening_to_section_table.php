<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('section', function (Blueprint $table) {
            $table->boolean('sec_is_evening')->default(false)->after('sec_year_level');
        });
    }

    public function down(): void
    {
        Schema::table('section', function (Blueprint $table) {
            $table->dropColumn('sec_is_evening');
        });
    }
};