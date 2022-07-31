<?php

/**
 * This goal of this script is to produce a list of dates and for each of those dates show how many
 * of the "entry" windows were in the 180 days prior to that date.
 */

use Antriver\DaysCalculator\Utils;
use Spatie\Period\Period;

require_once __DIR__.'/vendor/autoload.php';

date_default_timezone_set('Etc/Utc');

$windowSize = 180;

if (php_sapi_name() == "cli") {
    // Simulate GET params to test
    $_GET['entries'] = '2021-09-23,2021-12-13,2022-01-07,2022-01-11,2022-03-08,2022-03-10,2022-03-27,2022-04-03,2022-04-26,2022-05-14,2022-06-02,2022-06-08,2022-07-20,2022-07-26';
}

if (empty($_GET['entries'])) {
    die('A string of entries must be provided.');
}

/** @var Period[] $entryPeriods */
$entryPeriods = [];
$lastEntryDate = null;
foreach (explode(',', $_GET['entries']) as $entryDate) {
    if ($lastEntryDate === null) {
        $lastEntryDate = $entryDate;
    } else {
        $entryPeriods[] = Spatie\Period\Period::make(
            new DateTimeImmutable($lastEntryDate),
            new DateTimeImmutable($entryDate),
        );
        $lastEntryDate = null;
    }
}

$adjustedWindowSize = $windowSize - 1;

$firstPeriod = $entryPeriods[0];
$lastPeriod = $entryPeriods[count($entryPeriods) - 1];

$startDate = $firstPeriod->getStart();
$endDate = max(
    $lastPeriod->getEnd()->modify("+{$adjustedWindowSize} days"),
    (new DateTimeImmutable('midnight today'))->modify("+{$adjustedWindowSize} days")
);

$output = fopen("php://output", "a");

$currentDate = $startDate;
while ($currentDate <= $endDate) {
    // Starting from this date count back 180 days.
    $currentDateWindowStart = $currentDate->modify("-{$adjustedWindowSize} days");

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
