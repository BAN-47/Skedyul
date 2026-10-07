<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faculty', function (Blueprint $table) {
            if (!Schema::hasColumn('faculty', 'fac_special_position')) {
                $table->string('fac_special_position')->nullable();
            }
            if (!Schema::hasColumn('faculty', 'fac_special_position_max_hours')) {
                $table->decimal('fac_special_position_max_hours', 5, 2)->nullable();
            }
        });

        DB::statement(<<<'SQL'
            UPDATE public.faculty
            SET fac_special_position_max_hours = CASE fac_special_position
                WHEN 'Vice-President' THEN 3
                WHEN 'University Director' THEN 6
                WHEN 'Campus Director' THEN 6
                WHEN 'Assistant Campus Director' THEN 9
                WHEN 'Dean of Instruction' THEN 9
                WHEN 'College Dean' THEN 9
                WHEN 'Associate College Dean' THEN 12
                WHEN 'Department SUC Function Chairperson' THEN 15
                WHEN 'Campus Secretary' THEN 15
                ELSE NULL
            END
            WHERE fac_special_position IS NOT NULL
        SQL);

        DB::statement(<<<'SQL'
            DO $$ BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_constraint
                    WHERE conname = 'faculty_special_position_cap_check'
                      AND conrelid = 'public.faculty'::regclass
                ) THEN
                    ALTER TABLE public.faculty
                    ADD CONSTRAINT faculty_special_position_cap_check CHECK (
                        (fac_special_position IS NULL AND fac_special_position_max_hours IS NULL)
                        OR (
                            fac_special_position IS NOT NULL
                            AND fac_special_position_max_hours IS NOT NULL
                            AND CASE fac_special_position
                                WHEN 'Vice-President' THEN fac_special_position_max_hours = 3
                                WHEN 'University Director' THEN fac_special_position_max_hours = 6
                                WHEN 'Campus Director' THEN fac_special_position_max_hours = 6
                                WHEN 'Assistant Campus Director' THEN fac_special_position_max_hours = 9
                                WHEN 'Dean of Instruction' THEN fac_special_position_max_hours = 9
                                WHEN 'College Dean' THEN fac_special_position_max_hours = 9
                                WHEN 'Associate College Dean' THEN fac_special_position_max_hours = 12
                                WHEN 'Department SUC Function Chairperson' THEN fac_special_position_max_hours = 15
                                WHEN 'Campus Secretary' THEN fac_special_position_max_hours = 15
                                ELSE FALSE
                            END
                        )
                    );
                END IF;
            END $$;
        SQL);
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE public.faculty DROP CONSTRAINT IF EXISTS faculty_special_position_cap_check');
        Schema::table('faculty', function (Blueprint $table) {
            $columns = array_values(array_filter(
                ['fac_special_position', 'fac_special_position_max_hours'],
                fn ($column) => Schema::hasColumn('faculty', $column)
            ));
            if ($columns) $table->dropColumn($columns);
        });
    }
};
