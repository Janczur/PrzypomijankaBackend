<?php


namespace App\Modules\Remembrall\Message;


use App\Modules\Remembrall\Entity\Reminder;

interface SendReminderInterface
{
    public function getReminder(): Reminder;
}