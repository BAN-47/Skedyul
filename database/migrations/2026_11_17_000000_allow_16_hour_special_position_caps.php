<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE public.faculty DROP CONSTRAINT IF EXISTS faculty_special_position_cap_check');

        DB::statement(<<<'SQL'
            UPDATE public.faculty
            SET fac_special_position = 'Department Chairperson',
                fac_special_position_max_hours = 16
            WHERE fac_special_position = 'Department SUC Function Chairperson'
        SQL);

        DB::statement(<<<'SQL'
            UPDATE public.faculty
            SET fac_special_position_max_hours = 16
            WHERE fac_special_position IN (
                'Department Chairperson',
                'PERG Chair',
                'Campus Secretary',
                'College Secretary'
            )
        SQL);

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
                        WHEN 'Department Chairperson' THEN fac_special_position_max_hours = 16
                        WHEN 'PERG Chair' THEN fac_special_position_max_hours = 16
                        WHEN 'Campus Secretary' THEN fac_special_position_max_hours = 16
                        WHEN 'College Secretary' THEN fac_special_position_max_hours = 16
                        ELSE FALSE
                    END
                )
            )
        SQL);
    }

    public function down(): void
    {
        $hasNewCaps = DB::table('faculty')
            ->whereIn('fac_special_position', [
                'Department Chairperson',
                'Campus Secretary',
                'College Secretary',
            ])
            ->where('fac_special_position_max_hours', 16)
            ->exists();

        if ($hasNewCaps) {
            throw new RuntimeException(
                'Cannot roll back 16-hour faculty special-position caps while faculty records use them.'
            );
        }

        DB::statement('ALTER TABLE public.faculty DROP CONSTRAINT IF EXISTS faculty_special_position_cap_check');

        DB::statement(<<<'SQL'
            UPDATE public.faculty
            SET fac_special_position = 'Department SUC Function Chairperson',
                fac_special_position_max_hours = 15
            WHERE fac_special_position = 'Department Chairperson'
        SQL);

        DB::statement(<<<'SQL'
            UPDATE public.faculty
            SET fac_special_position_max_hours = 15
            WHERE fac_special_position IN ('Campus Secretary', 'College Secretary')
        SQL);

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
};
