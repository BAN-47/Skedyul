<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('USER', function (Blueprint $table) {
            $table->string('usr_first_name')->nullable()->after('usr_name');
            $table->string('usr_last_name')->nullable()->after('usr_first_name');
            $table->string('usr_middle_name')->nullable()->after('usr_last_name');
            $table->string('usr_suffix')->nullable()->after('usr_middle_name');
            $table->string('usr_rank_title')->nullable()->after('usr_suffix');
            $table->string('usr_employee_id')->nullable()->after('usr_rank_title');
            $table->string('usr_gender')->nullable()->after('usr_employee_id');
            $table->string('usr_civil_status')->nullable()->after('usr_gender');
            $table->date('usr_dob')->nullable()->after('usr_civil_status');
            $table->string('usr_nationality')->nullable()->after('usr_dob');
        });
    }

    public function down(): void
    {
        Schema::table('USER', function (Blueprint $table) {
            $table->dropColumn([
                'usr_first_name',
                'usr_last_name',
                'usr_middle_name',
                'usr_suffix',
                'usr_rank_title',
                'usr_employee_id',
                'usr_gender',
                'usr_civil_status',
                'usr_dob',
                'usr_nationality',
            ]);
        });
    }
};