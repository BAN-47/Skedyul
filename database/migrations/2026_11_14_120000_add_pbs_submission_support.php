<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PBS_SECTION_UNIQUE = 'schedule_submission_pbs_section_unique';
    private const TYPE_SCOPE_INDEX = 'schedule_submission_type_scope_idx';

    public function up(): void
    {
        Schema::table('schedule_submission', function (Blueprint $table) {
            $table->string('schsub_type', 10)->default('pbt');
            $table->uuid('schsub_sec_id')->nullable();
            $table->foreign('schsub_sec_id')
                ->references('sec_id')
                ->on('section')
                ->nullOnDelete();
            $table->index(
                ['schsub_type', 'schsub_dept_id', 'schsub_sem_id'],
                self::TYPE_SCOPE_INDEX
            );
        });

        DB::statement(
            "CREATE UNIQUE INDEX " . self::PBS_SECTION_UNIQUE .
                " ON schedule_submission (schsub_dept_id, schsub_sem_id, schsub_sec_id)" .
                " WHERE schsub_type = 'pbs' AND schsub_sec_id IS NOT NULL"
        );
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS ' . self::PBS_SECTION_UNIQUE);

        Schema::table('schedule_submission', function (Blueprint $table) {
            $table->dropIndex(self::TYPE_SCOPE_INDEX);
            $table->dropForeign(['schsub_sec_id']);
            $table->dropColumn(['schsub_type', 'schsub_sec_id']);
        });
    }
};
