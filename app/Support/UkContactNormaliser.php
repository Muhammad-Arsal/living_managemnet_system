<?php

namespace App\Support;

class UkContactNormaliser
{
    public static function postcode(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $compact = strtoupper((string) preg_replace('/\s+/', '', $value));

        if ($compact === '') {
            return null;
        }

        if (strlen($compact) < 5) {
            return $compact;
        }

        return substr($compact, 0, -3).' '.substr($compact, -3);
    }

    public static function mobile(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        if ($trimmed === '') {
            return null;
        }

        $digits = (string) preg_replace('/[^\d+]/', '', $trimmed);

        if (str_starts_with($digits, '+44')) {
            $digits = '0'.substr($digits, 3);
        } elseif (str_starts_with($digits, '44') && strlen($digits) >= 11) {
            $digits = '0'.substr($digits, 2);
        }

        return $digits;
    }
}
