<?php

namespace App\Services;

class MiddleInitialFormatter
{
    public static function format(?string $middleName): ?string
    {
        $middleName = trim((string) $middleName);

        if ($middleName === '' || ! preg_match('/\p{L}/u', $middleName, $matches)) {
            return null;
        }

        return mb_strtoupper($matches[0], 'UTF-8') . '.';
    }
}
