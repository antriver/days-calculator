<?php

use Antriver\DaysCalculator\Utils;
use Spatie\Period\Period;

class UtilsTest extends \PHPUnit\Framework\TestCase
{
    private $testEntryPeriods;

    protected function setUp(): void
    {
        parent::setUp();

        $entries = [
            ['2021-09-23', '2021-12-13'],
            ['2022-01-07', '2022-01-11'],
            ['2022-03-08', '2022-03-10'],
            ['2022-03-27', '2022-04-03'],
            ['2022-04-26', '2022-05-14'],
            ['2022-06-02', '2022-06-08'],
            ['2022-07-20', '2022-07-26'],
        ];

        $this->testEntryPeriods = array_map(
            function (array $row) {
                return Spatie\Period\Period::make(
                    new DateTimeImmutable($row[0]),
                    new DateTimeImmutable($row[1]),
                );
            },
            $entries
        );
    }

    public function testCreatePeriodsFromCommaSeparatedDates()
    {
        $string = '2021-09-23,2021-12-13,2022-01-07,2022-01-11';

        $result = Utils::createPeriodsFromCommaSeparatedDates($string);

        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(Period::class, $result);

        $this->assertSame('2021-09-23', $result[0]->getStart()->format('Y-m-d'));
        $this->assertSame('2021-12-13', $result[0]->getEnd()->format('Y-m-d'));
        $this->assertSame('2022-01-07', $result[1]->getStart()->format('Y-m-d'));
        $this->assertSame('2022-01-11', $result[1]->getEnd()->format('Y-m-d'));
    }

    public function testCalculateDaysInPeriod()
    {
        $testPeriod = Spatie\Period\Period::make(
            (new DateTimeImmutable('2022-07-30'))->modify('-179 days'),
            new DateTimeImmutable('2022-07-30')
        );

        $result = Utils::calculateDaysInPeriod(
            $testPeriod,
            $this->testEntryPeriods
        );

        $this->assertSame(
            [
                0,
                0,
                3,
                8,
                19,
                7,
                7,
            ],
            $result
        );
    }

    public function testIsDateInAnyPeriod()
    {
        $this->assertTrue(
            Utils::isDateInAnyPeriod(
                new DateTime('2021-10-01'),
                $this->testEntryPeriods
            )
        );

        $this->assertTrue(
            Utils::isDateInAnyPeriod(
                new DateTime('2022-07-26'),
                $this->testEntryPeriods
            )
        );

        $this->assertFalse(
            Utils::isDateInAnyPeriod(
                new DateTime('2022-07-27'),
                $this->testEntryPeriods
            )
        );
    }
}
