<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faculty_account_reviews', function (Blueprint $table) {
            $table->string('fvr_applicant_name')->nullable();
            $table->string('fvr_applicant_email')->nullable();
            $table->text('fvr_decision_note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('faculty_account_reviews', function (Blueprint $table) {
            $table->dropColumn([
                'fvr_applicant_name',
                'fvr_applicant_email',
                'fvr_decision_note',
            ]);
        });
    }
};
