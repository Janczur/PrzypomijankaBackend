<?php

namespace App\Modules\Remembrall\Message;

use App\Modules\Remembrall\Entity\Reminder;

final class SendPreReminderEmail implements SendReminderInterface
{

    private Reminder $reminder;

    public function __construct(Reminder $reminder)
    {
        $this->reminder = $reminder;
    }

    public function getReminder(): Reminder
    {
        return $this->reminder;
    }

}
