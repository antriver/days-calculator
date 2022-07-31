<?php

/**
 * This goal of this script is to produce a list of dates and for each of those dates show how many
 * of the "entry" windows were in the 180 days prior to that date.
 */

use Antriver\DaysCalculator\Utils;
use Spatie\Period\Period;

require_once __DIR__.'/../bootstrap.php';

if (empty($_GET['entries'])) {
    die('A string of entries must be provided.');
}

$entryPeriods = Utils::createPeriodsFromCommaSeparatedDates($_GET['entries']);

$range = Utils::calculateDesiredOutputRange($entryPeriods);
$startDate = $range->getStart();
$endDate = $range->getEnd();

$output = fopen("php://output", "a");

$currentDate = $startDate;
while ($currentDate <= $endDate) {
    // Starting from this date count back 180 days.
    $currentDateWindowStart = $currentDate->modify('-'.ADJUSTED_WINDOW_SIZE.' days');

    $currentPeriod = Period::make(
        $currentDateWindowStart,
        $currentDate
    );

    $daysInPeriod = array_sum(
        Utils::calculateDaysInPeriod(
            $currentPeriod,
            $entryPeriods
        )
    );

    $currentDateIsInAnEntryPeriod = Utils::isDaysInAnyPeriod(
        $currentDate,
        $entryPeriods
    );

    fputcsv(
        $output,
        [
            $currentDate->format('Y-m-d'),
            $daysInPeriod,
            $currentDateIsInAnEntryPeriod ? 1 : 0,
        ]
    );

    $currentDate = $currentDate->modify('+1 day');
}
