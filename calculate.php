<?php

/**
 * This goal of this script is to produce a list of dates and for each of those dates show how many
 * of the "entry" windows were in the 180 days prior to that date.
 */

use Spatie\Period\Period;

require_once __DIR__.'/vendor/autoload.php';

date_default_timezone_set('Etc/Utc');

$entries = [
    ['2021-09-23', '2021-12-13'],
    ['2022-01-07', '2022-01-11'],
    ['2022-03-08', '2022-03-10'],
    ['2022-03-27', '2022-04-03'],
    ['2022-04-26', '2022-05-14'],
    ['2022-06-02', '2022-06-08'],
    ['2022-07-20', '2022-07-26'],
];

$windowSize = 180;

// No more options to change below here.

$entryPeriods = array_map(
    function (array $row) {
        return Spatie\Period\Period::make(
            new DateTimeImmutable($row[0]),
            new DateTimeImmutable($row[1]),
        );
    },
    $entries
);

$adjustedWindowSize = $windowSize - 1;

$firstPeriod = $entryPeriods[0];
$lastPeriod = $entryPeriods[count($entryPeriods) - 1];

$startDate = $firstPeriod->start();
$endDate = max(
    $lastPeriod->end()->modify("+{$adjustedWindowSize} days"),
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
        \Antriver\DaysCalculator\Utils::calculateDaysInPeriod(
            $currentPeriod,
            $entryPeriods
        )
    );

    fputcsv(
        $output,
        [
            $currentDate->format('Y-m-d'),
            $daysInPeriod,
        ]
    );

    $currentDate = $currentDate->modify('+1 day');
}
