<?php

namespace App\Helpers;

use Carbon\Carbon;
use Throwable;

class AgeHelper
{
    /**
     * Calculate whole completed years between birth date and reference date.
     * Safe for Carbon 3 (where diffInYears returns signed float).
     */
    public static function calculateWholeYears(mixed $birthDate, mixed $referenceDate = null): ?int
    {
        if (empty($birthDate)) {
            return null;
        }

        try {
            $birth = Carbon::parse($birthDate)->startOfDay();
            $reference = !empty($referenceDate)
                ? Carbon::parse($referenceDate)->startOfDay()
                : Carbon::now()->startOfDay();

            if ($birth->greaterThan($reference)) {
                return null;
            }

            return (int) floor($birth->diffInYears($reference));
        } catch (Throwable) {
            return null;
        }
    }
}
