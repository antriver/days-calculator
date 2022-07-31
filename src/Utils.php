<?php

namespace Antriver\DaysCalculator;

use DateTimeInterface;
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

                return count($overlapPeriod) > 0 ? $overlapPeriod[0]->length() : 0;
            },
            $entryPeriods
        );
    }

    /**
     * @param DateTimeInterface $date
     * @param Period[] $entryPeriods
     *
     * @return bool
     */
    public static function isDaysInAnyPeriod(
        DateTimeInterface $date,
        array $entryPeriods
    ): bool {
        foreach ($entryPeriods as $entryPeriod) {
            if ($entryPeriod->contains($date)) {
                return true;
            }
        }

        return false;
    }
}
