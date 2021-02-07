<?php


namespace App\Modules\Remembrall\Utils;


use App\Modules\Remembrall\Entity\Reminder;
use DateTimeInterface;

interface ReminderTimeCalculatorInterface
{
    public function getRemainingMillisecondsUntil(DateTimeInterface $dateTime): int;

    public function calculateNextReminderDate(Reminder $reminder): DateTimeInterface;
}