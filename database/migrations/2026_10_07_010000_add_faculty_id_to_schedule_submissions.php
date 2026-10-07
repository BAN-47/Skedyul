<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const FACULTY_UNIQUE = 'schedule_submission_dept_sem_fac_unique';
    private const OLD_UNIQUE = 'schedule_submission_schsub_dept_id_schsub_sem_id_key';

    public function up(): void
    {
        DB::statement(
            'ALTER TABLE schedule_submission DROP CONSTRAINT IF EXISTS ' . self::OLD_UNIQUE
        );

        Schema::table('schedule_submission', function (Blueprint $table) {
            $table->uuid('schsub_fac_id')->nullable();

            $table->foreign('schsub_fac_id')
                ->references('fac_id')
                ->on('faculty')
                ->nullOnDelete();

            $table->unique(
                ['schsub_dept_id', 'schsub_sem_id', 'schsub_fac_id'],
                self::FACULTY_UNIQUE
            );
        });
    }

    public function down(): void
    {
        Schema::table('schedule_submission', function (Blueprint $table) {
            $table->dropUnique(self::FACULTY_UNIQUE);
            $table->dropForeign(['schsub_fac_id']);
            $table->dropColumn('schsub_fac_id');
        });

        DB::statement(
            'ALTER TABLE schedule_submission ADD CONSTRAINT ' .
                self::OLD_UNIQUE .
                ' UNIQUE (schsub_dept_id, schsub_sem_id)'
        );
    }
};
