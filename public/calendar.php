<?php

use Antriver\DaysCalculator\Utils;

require_once __DIR__.'/../bootstrap.php';

$entryPeriods = Utils::createPeriodsFromCommaSeparatedDates($_GET['entries']);

$range = Utils::calculateDesiredOutputRange($entryPeriods);
$startDate = $range->getStart();
$endDate = $range->getEnd();

function formatDateForCalendar(DateTimeInterface $date): string
{
    global $entryPeriods;

    $str = ' '.$date->format('j');
    if (Utils::isDateInAnyPeriod($date, $entryPeriods)) {
        $str .= '*';
    } else {
        $str .= ' ';
    }

    return $str;
}

$weekdays = [
    'Mon',
    'Tue',
    'Wed',
    'Thu',
    'Fri',
    'Sat',
    'Sun',
];

$output = fopen("php://output", "a");

$result = [
    array_merge([''], $weekdays),
];

$currentMonth = null;
$currentWeek = [];
$currentDate = $startDate;
while ($currentDate <= $endDate) {
    if (!$currentMonth || $currentDate->format('n') !== $currentMonth) {
        $currentWeek[0] = $currentDate->format('F Y');
    }
    $currentMonth = $currentDate->format('n');

    $dayOfWeek = (int) $currentDate->format('N');

    // For the first week of the calendar we need to align the days correctly under
    // the weekdays.
    if (count($result) === 1 && count($currentWeek) === 1) {
        for ($i = 1; $i < $dayOfWeek; $i++) {
            $currentWeek[$i] = '';
        }
    }

    // Add current day to week.
    $currentWeek[$dayOfWeek] = formatDateForCalendar($currentDate);

    if ($dayOfWeek === 7) {
        ksort($currentWeek);

        // Add week to output.
        $result[] = $currentWeek;
        // Start a new week.
        $currentWeek = [
            '',
        ];
    }

    $currentDate = $currentDate->modify('+1 day');
}

// print_r($result);

foreach ($result as $line) {
    fputcsv($output, $line);
}
