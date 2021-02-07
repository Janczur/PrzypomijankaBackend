<?php


namespace App\Modules\Remembrall\Utils;


use App\Modules\Remembrall\Entity\Reminder;
use DateInterval;
use DateTime;
use DateTimeInterface;

class ReminderTimeCalculator implements ReminderTimeCalculatorInterface
{

    public function getRemainingMillisecondsUntil(DateTimeInterface $dateTime): int
    {
        $diffSeconds = $dateTime->getTimestamp() - (new DateTime())->getTimestamp();
        return $diffSeconds * 1000;
    }

    public function calculateNextReminderDate(Reminder $reminder): DateTimeInterface
    {
        $cyclic = $reminder->getCyclic();
        $periodicity = $cyclic->getPeriodicity();
        $cyclicType = $cyclic->getType()->getFirstLetterOfTypeName();
        $now = new DateTime();
        $interval = new DateInterval("P{$periodicity}{$cyclicType}");
        return $now->add($interval);
    }
}