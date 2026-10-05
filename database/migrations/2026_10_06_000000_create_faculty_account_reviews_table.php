<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faculty_account_reviews', function (Blueprint $table) {
            $table->uuid('fvr_id')->primary();
            $table->uuid('fvr_usr_id')->unique();
            $table->string('fvr_id_photo_path', 500);
            $table->string('fvr_status', 20)->default('pending')->index();
            $table->uuid('fvr_reviewed_by')->nullable();
            $table->timestamp('fvr_reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faculty_account_reviews');
    }
};
