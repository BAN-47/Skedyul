<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faculty', function (Blueprint $table) {
            $table->string('fac_bachelors_abbr', 40)->nullable();
            $table->string('fac_masters_abbr', 40)->nullable();
            $table->string('fac_doctorate_abbr', 60)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('faculty', function (Blueprint $table) {
            $table->dropColumn([
                'fac_bachelors_abbr',
                'fac_masters_abbr',
                'fac_doctorate_abbr',
            ]);
        });
    }
};
