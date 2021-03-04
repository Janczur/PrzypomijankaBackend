<?php


namespace App\Modules\Remembrall\Repository;


use App\Modules\Remembrall\Entity\PreReminder;
use DateTimeInterface;

interface PreReminderRepositoryInterface
{
    /**
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @return PreReminder[]
     */
    public function getAllPreRemindersToBeSendBetween(DateTimeInterface $from, DateTimeInterface $to): array;
}