<?php


namespace App\Modules\Remembrall\Utils;


use App\Modules\Remembrall\Entity\Reminder;
use DateInterval;
use DateTime;
use DateTimeInterface;

final class ReminderCalculator
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
        $cyclicType = $cyclic->getFirstLetterOfTypeName();
        $remindAt = clone $reminder->getRemindAt();
        $interval = new DateInterval("P{$periodicity}{$cyclicType}");
        return $remindAt->add($interval);
    }

    public function calculateNextPreReminderDate(Reminder $reminder): DateTimeInterface
    {
        $remindBeforeDays = $reminder->getPreReminder()->getDaysBefore();
        $interval = new DateInterval("P{$remindBeforeDays}D");
        $remindAt = clone $reminder->getRemindAt();
        return $remindAt->sub($interval);
    }
}