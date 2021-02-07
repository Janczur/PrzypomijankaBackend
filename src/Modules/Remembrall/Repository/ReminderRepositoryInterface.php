<?php


namespace App\Modules\Remembrall\Repository;


use App\Modules\Remembrall\Entity\Reminder;
use DateTimeInterface;

interface ReminderRepositoryInterface
{

    /**
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @return Reminder[]
     */
    public function getAllPreRemindersToBeSendBetween(DateTimeInterface $from, DateTimeInterface $to): array;

    /**
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @return Reminder[]
     */
    public function getAllRemindersToBeSendBetween(DateTimeInterface $from, DateTimeInterface $to): array;
}