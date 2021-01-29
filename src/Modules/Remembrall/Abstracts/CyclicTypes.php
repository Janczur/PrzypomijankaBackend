<?php


namespace App\Modules\Remembrall\Abstracts;


abstract class CyclicTypes
{
    public const DAY = 1;
    public const WEEK = 2;
    public const MONTH = 3;
    public const QUARTER = 4;
    public const YEAR = 5;

    public static array $display = [
        self::DAY => 'Dzień',
        self::WEEK => 'Tydzień',
        self::MONTH => 'Miesiąc',
        self::QUARTER => 'Kwartał',
        self::YEAR => 'Rok',
    ];

    public static function getTypeNames(): array
    {
        return self::$display;
    }
}