<?php


namespace App\Modules\Remembrall\Abstracts;


abstract class CyclicTypes
{
    public const DAY = 1;
    public const WEEK = 2;
    public const MONTH = 3;
    public const YEAR = 4;

    public static array $display = [
        self::DAY => 'Day',
        self::WEEK => 'Week',
        self::MONTH => 'Month',
        self::YEAR => 'Year',
    ];

    public static function getTypeNames(): array
    {
        return self::$display;
    }
}