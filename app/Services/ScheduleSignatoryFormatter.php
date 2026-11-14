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

        $faculty = $person->user?->faculty;
        $postNominals = [];
        foreach ([
            $faculty?->fac_bachelors_degree,
            $faculty?->fac_masters_degree,
            $faculty?->fac_doctorate_degree,
        ] as $degree) {
            foreach (preg_split('/\s*[\/;]\s*/', trim((string) $degree)) ?: [] as $credential) {
                $abbreviation = $this->abbreviateDegree($credential);
                if ($abbreviation !== '') {
                    $postNominals[] = $abbreviation;
                }
            }
        }

        return implode(', ', array_filter([$name, $suffix, ...array_unique($postNominals)]));
    }

    private function abbreviateDegree(string $degree): string
    {
        $degree = trim($degree);
        if ($degree === '') {
            return '';
        }

        $normalized = mb_strtolower(preg_replace('/[^\pL\pN]+/u', ' ', $degree) ?? '');
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        if ($normalized === 'doctor of philosophy') {
            return 'PhD';
        }

        $level = '';
        $remainder = $normalized;
        if (preg_match('/^(bs|ba|bsc|ms|ma|msc|mit|dit|phd)\s+(?:in|of)\s+(.+)$/i', $degree, $matches)) {
            $level = mb_strtoupper($matches[1]);
            $remainder = mb_strtolower($matches[2]);
        } elseif (preg_match('/^(bachelor|master|doctor)(?:\s+of|\s+in)?\s+(.+)$/', $normalized, $matches)) {
            $level = match ($matches[1]) {
                'bachelor' => 'B',
                'master' => 'M',
                'doctor' => 'D',
            };
            $remainder = $matches[2];
        } elseif (preg_match('/^[A-Za-z0-9.\-]{2,15}$/', $degree)) {
            return preg_replace('/[.]+/', '', $degree) ?? $degree;
        }

        $words = preg_split('/\s+/', trim($remainder)) ?: [];
        $words = array_values(array_filter(
            $words,
            static fn ($word) => !in_array($word, ['of', 'in', 'and', 'the'], true)
        ));
        if ($words === []) {
            return $degree;
        }

        $field = implode('', array_map(static function ($word): string {
            $word = match ($word) {
                'engg', 'eng' => 'engineering',
                'tech' => 'technology',
                'sci' => 'science',
                default => $word,
            };

            return mb_strtoupper(mb_substr($word, 0, 1));
        }, $words));

        return $level . $field;
    }
}
