<?php

require_once __DIR__.'/vendor/autoload.php';

date_default_timezone_set('Etc/Utc');

// The number of days to perform entry calculations for.
const WINDOW_SIZE = 180;

// If you add/subtract this number of days to/from a date you get a range of 180 days instead of 181 days.
const ADJUSTED_WINDOW_SIZE = WINDOW_SIZE - 1;

if (php_sapi_name() == "cli") {
    // Simulate GET params to test
    $_GET['entries'] = '2021-09-23,2021-12-13,2022-01-07,2022-01-11,2022-03-08,2022-03-10,2022-03-27,2022-04-03,2022-04-26,2022-05-14,2022-06-02,2022-06-08,2022-07-20,2022-07-26';
}
