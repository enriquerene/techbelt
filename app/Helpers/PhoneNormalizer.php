<?php

namespace App\Helpers;

class PhoneNormalizer
{
    /**
     * Normalize a Brazilian phone number to DDD + number only (10 or 11 digits).
     * Accepts various formats: (21) 96846-4381, 21968464381, +5521968464381, etc.
     * Returns the normalized phone (DDD + number) or null if invalid.
     */
    public static function normalize(string $phone): ?string
    {
        // Remove all non-digit characters
        $digits = preg_replace('/[^\d]/', '', $phone);

        // Remove leading zeros
        $digits = ltrim($digits, '0');

        // If starts with country code 55, remove it
        if (str_starts_with($digits, '55')) {
            $digits = substr($digits, 2);
        }

        // Remove any remaining country code (e.g., 55 from +55)
        // Keep only 10 or 11 digits (DDD + number)
        if (strlen($digits) > 11) {
            // If still too long, try to extract last 10 or 11 digits
            // This handles cases like 5521968464381 (13 digits)
            if (strlen($digits) === 13 && str_starts_with($digits, '55')) {
                $digits = substr($digits, 2); // Remove 55
            } else {
                // Take last 11 digits (mobile) or 10 digits (landline)
                $digits = substr($digits, -11);
            }
        }

        // Validate length: should be 10 (landline) or 11 (mobile)
        if (!in_array(strlen($digits), [10, 11])) {
            return null;
        }

        // Ensure DDD is valid (11 to 99)
        $ddd = substr($digits, 0, 2);
        if (!is_numeric($ddd) || $ddd < 11 || $ddd > 99) {
            return null;
        }

        return $digits;
    }

    /**
     * Format phone for display (Brazilian format).
     * Expects 10 or 11 digits (DDD + number).
     */
    public static function formatForDisplay(string $phone): string
    {
        // First normalize to ensure we have clean digits
        $digits = self::normalize($phone);
        
        if (!$digits) {
            // If can't normalize, return original
            return $phone;
        }

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

        // Fallback: return digits as is
        return $digits;
    }
}