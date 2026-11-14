<?php

namespace App\Services;

class ScheduleSignatoryFormatter
{
    public function formatRoleName(?object $person, string $prefix): string
    {
        if (!$person) {
            return '';
        }

        $first = trim((string) ($person->{$prefix . 'first_name'} ?? ''));
        $middle = trim((string) ($person->{$prefix . 'middle_name'} ?? ''));
        $last = trim((string) ($person->{$prefix . 'last_name'} ?? ''));
        $suffix = trim((string) ($person->{$prefix . 'suffix'} ?? ''));
        if ($suffix === '') {
            $suffix = trim((string) ($person->user?->usr_suffix ?? ''));
        }

        $name = implode(' ', array_filter([
            $first,
            $middle !== '' ? mb_strtoupper(mb_substr($middle, 0, 1)) . '.' : '',
            $last,
        ]));

        $formatted = implode(', ', array_filter([$name, $suffix]));
        $postNominal = FacultyNameFormatter::highestPostNominal($person->user?->faculty);

        return $formatted . ($postNominal !== '' ? ' (' . $postNominal . ')' : '');
    }
}
