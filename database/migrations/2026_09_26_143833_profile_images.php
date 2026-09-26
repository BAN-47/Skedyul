<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dean', function (Blueprint $table) {
            $table->string('dean_profile_image_public_id')->nullable()->after('dean_profile_image');
        });
    }

    public function down(): void
    {
        Schema::table('dean', function (Blueprint $table) {
            $table->dropColumn('dean_profile_image_public_id');
        });
    }
};