<?php

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

    public function testCalculateDaysInPeriod()
    {
        $testPeriod = Spatie\Period\Period::make(
            (new DateTimeImmutable('2022-07-30'))->modify('-179 days'),
            new DateTimeImmutable('2022-07-30')
        );

        $result = \Antriver\DaysCalculator\Utils::calculateDaysInPeriod(
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
                7
            ],
            $result
        );
    }
}
