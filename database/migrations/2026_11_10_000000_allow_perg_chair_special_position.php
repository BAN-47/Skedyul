<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE public.faculty DROP CONSTRAINT IF EXISTS faculty_special_position_cap_check');
        DB::table('faculty')
            ->where('fac_special_position', 'PERG Chair')
            ->update(['fac_special_position_max_hours' => 16]);

        DB::statement(<<<'SQL'
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
                        WHEN 'PERG Chair' THEN fac_special_position_max_hours = 16
                        WHEN 'Campus Secretary' THEN fac_special_position_max_hours = 15
                        WHEN 'College Secretary' THEN fac_special_position_max_hours = 15
                        ELSE FALSE
                    END
                )
            )
        SQL);
    }

    public function down(): void
    {
        if (DB::table('faculty')->where('fac_special_position', 'PERG Chair')->exists()) {
            throw new RuntimeException(
                'Cannot roll back the PERG Chair limit migration while faculty still have that special position.'
            );
        }

        DB::statement('ALTER TABLE public.faculty DROP CONSTRAINT IF EXISTS faculty_special_position_cap_check');

        DB::statement(<<<'SQL'
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
                        WHEN 'College Secretary' THEN fac_special_position_max_hours = 15
                        ELSE FALSE
                    END
                )
            )
        SQL);
    }
};
