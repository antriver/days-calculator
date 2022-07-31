<?php

namespace Antriver\DaysCalculator;

use DateTimeImmutable;
use DateTimeInterface;
use Spatie\Period\Period;

class Utils
{
    /**
     * Creates an array of Periods from a flat list of comma separated dates, which should look like:
     * startDate,endDate,startDate,endDate,startDate,endDate...
     *
     * @param string $dates
     *
     * @return Period[]
     */
    public static function createPeriodsFromCommaSeparatedDates(string $dates): array
    {
        /** @var Period[] $entryPeriods */
        $periods = [];
        $lastStartDate = null;
        foreach (explode(',', $dates) as $date) {
            if ($lastStartDate === null) {
                $lastStartDate = $date;
            } else {
                $periods[] = Period::make(
                    new DateTimeImmutable($lastStartDate),
                    new DateTimeImmutable($date),
                );
                $lastStartDate = null;
            }
        }

        return $periods;
    }

    /**
     * @param Period $periods
     *
     * @return Period
     */
    public static function calculateDesiredOutputRange(array $periods): Period
    {
        $firstPeriod = $periods[0];
        $lastPeriod = $periods[count($periods) - 1];

        $startDate = $firstPeriod->getStart();
        $endDate = max(
            $lastPeriod->getEnd()->modify('+'.ADJUSTED_WINDOW_SIZE.' days'),
            (new DateTimeImmutable('midnight today'))->modify('+'.ADJUSTED_WINDOW_SIZE.' days')
        );

        return Period::make(
            $startDate->modify('first day of this month'),
            $endDate->modify('last day of this month'),
        );
    }

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
    public static function isDateInAnyPeriod(
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
