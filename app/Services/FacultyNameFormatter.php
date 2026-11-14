<?php

namespace App\Services;

/** Formats faculty names consistently, adding only their highest saved degree acronym. */
class FacultyNameFormatter
{
    public static function format(?object $faculty): string
    {
        if (!$faculty) {
            return '';
        }

        $first = trim((string) ($faculty->fac_first_name ?? ''));
        $middle = trim((string) ($faculty->fac_middle_name ?? ''));
        $last = trim((string) ($faculty->fac_last_name ?? ''));
        $suffix = trim((string) ($faculty->fac_suffix ?? ''));

        $name = trim(implode(' ', array_filter([$first, $middle, $last])));
        if ($name === '') {
            $user = $faculty->user ?? null;
            $first = trim((string) ($user?->usr_first_name ?? ''));
            $middle = trim((string) ($user?->usr_middle_name ?? ''));
            $last = trim((string) ($user?->usr_last_name ?? ''));
            $suffix = trim((string) ($faculty->fac_suffix ?? $user?->usr_suffix ?? ''));
            $name = trim(implode(' ', array_filter([$first, $middle, $last])));
        }
        if ($name === '') {
            $user ??= $faculty->user ?? null;
            $name = trim((string) ($user?->usr_name ?? $faculty->full_name ?? ''));
        }
        if ($name !== '' && $suffix !== '') {
            $name .= ', ' . $suffix;
        }

        $postNominal = self::highestPostNominal($faculty);
        if ($name !== '' && $postNominal !== '') {
            $name .= ' (' . $postNominal . ')';
        }

        return $name;
    }

    public static function highestPostNominal(?object $faculty): string
    {
        if (!$faculty) {
            return '';
        }

        $doctorate = trim((string) ($faculty->fac_doctorate_degree ?? ''));
        $doctorateAbbr = trim((string) ($faculty->fac_doctorate_abbr ?? ''));
        if ($doctorate !== '' || $doctorateAbbr !== '') {
            return $doctorateAbbr;
        }

        $masters = trim((string) ($faculty->fac_masters_degree ?? ''));
        $mastersAbbr = trim((string) ($faculty->fac_masters_abbr ?? ''));
        if ($masters !== '' || $mastersAbbr !== '') {
            return $mastersAbbr;
        }

        return '';
    }
}
