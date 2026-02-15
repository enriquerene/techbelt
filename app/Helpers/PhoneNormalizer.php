<?php

namespace App\Helpers;

class PhoneNormalizer
{
    /**
     * Normalize a Brazilian phone number to E.164 format (+55XXXXXXXXXXX).
     * Accepts various formats: (21) 96846-4381, 21968464381, +5521968464381, etc.
     * Returns the normalized phone or null if invalid.
     */
    public static function normalize(string $phone): ?string
    {
        // Remove all non-digit characters except leading +
        $cleaned = preg_replace('/[^\d\+]/', '', $phone);

        // If starts with +, keep it
        $hasPlus = str_starts_with($cleaned, '+');
        if ($hasPlus) {
            $digits = substr($cleaned, 1);
        } else {
            $digits = $cleaned;
        }

        // Remove leading zeros
        $digits = ltrim($digits, '0');

        // If digits length is 10 (landline) or 11 (mobile) without country code
        if (in_array(strlen($digits), [10, 11])) {
            // Assume Brazil country code
            $digits = '55' . $digits;
        }

        // Ensure country code is present (Brazil is 55)
        if (!str_starts_with($digits, '55')) {
            // If digits already have another country code, keep as is
            // Otherwise prepend 55
            $digits = '55' . $digits;
        }

        // Final format: + followed by digits
        return '+' . $digits;
    }

    /**
     * Format phone for display (Brazilian format).
     */
    public static function formatForDisplay(string $phone): string
    {
        $normalized = self::normalize($phone);
        if (!$normalized) {
            return $phone;
        }

        // Remove +55
        $digits = substr($normalized, 3); // +55 -> length 3

        if (strlen($digits) === 10) {
            // Landline: (XX) XXXX-XXXX
            return sprintf('(%s) %s-%s',
                substr($digits, 0, 2),
                substr($digits, 2, 4),
                substr($digits, 6)
            );
        } elseif (strlen($digits) === 11) {
            // Mobile: (XX) 9XXXX-XXXX
            return sprintf('(%s) %s-%s',
                substr($digits, 0, 2),
                substr($digits, 2, 5),
                substr($digits, 7)
            );
        }

        // Fallback: return normalized
        return $normalized;
    }
}