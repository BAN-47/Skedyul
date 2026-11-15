<?php

namespace Tests\Unit;

use App\Services\ScheduleSignatoryFormatter;
use PHPUnit\Framework\TestCase;

class ScheduleSignatoryFormatterTest extends TestCase
{
    public function test_role_name_includes_saved_highest_degree_acronym(): void
    {
        $chair = (object) [
            'dc_first_name' => 'Noreen',
            'dc_middle_name' => 'B',
            'dc_last_name' => 'Fuentes',
            'dc_suffix' => null,
            'user' => (object) [
                'usr_suffix' => null,
                'faculty' => (object) [
                    'fac_doctorate_degree' => 'Doctor of Information Technology',
                    'fac_doctorate_abbr' => 'Ph.D., DIT',
                    'fac_masters_degree' => 'Master of Information Technology',
                    'fac_masters_abbr' => 'MIT',
                ],
            ],
        ];

        $this->assertSame(
            'Noreen B. Fuentes, Ph.D., DIT',
            (new ScheduleSignatoryFormatter())->formatRoleName($chair, 'dc_')
        );
    }

    public function test_role_name_keeps_suffix_when_no_degree_acronym_is_saved(): void
    {
        $dean = (object) [
            'dean_first_name' => 'Jose Maria',
            'dean_middle_name' => 'S',
            'dean_last_name' => 'Garcia',
            'dean_suffix' => 'II',
            'user' => (object) [
                'usr_suffix' => null,
                'faculty' => (object) [
                    'fac_doctorate_degree' => null,
                    'fac_doctorate_abbr' => null,
                    'fac_masters_degree' => null,
                    'fac_masters_abbr' => null,
                ],
            ],
        ];

        $this->assertSame(
            'Jose Maria S. Garcia, II',
            (new ScheduleSignatoryFormatter())->formatRoleName($dean, 'dean_')
        );
    }
}
