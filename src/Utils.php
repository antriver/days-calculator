<?php

namespace Antriver\DaysCalculator;

use Spatie\Period\Period;

class Utils
{
    /**
     * @param Period $period
     * @param Period[] $entryPeriods
     *
     * @return array
     */
    public static function calculateDaysInPeriod(
        Period $period,
        array $entryPeriods
    ): array {
        return array_map(
            function (Period $entryPeriod) use ($period) {
                $overlapPeriod = $period->overlap($entryPeriod);

                return $overlapPeriod ? $overlapPeriod->length() : 0;
            },
            $entryPeriods
        );
    }
}
