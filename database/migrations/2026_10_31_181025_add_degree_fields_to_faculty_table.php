<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('faculty', function (Blueprint $table) {
            $table->string('fac_bachelors_degree', 255)->nullable();
            $table->string('fac_masters_degree', 255)->nullable();
            $table->string('fac_doctorate_degree', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculty', function (Blueprint $table) {
            $table->dropColumn('fac_bachelors_degree');
            $table->dropColumn('fac_masters_degree');
            $table->dropColumn('fac_doctorate_degree');
        });
    }
};
