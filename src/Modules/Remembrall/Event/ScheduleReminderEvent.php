<?php


namespace App\Modules\Remembrall\Event;


use App\Modules\Remembrall\Entity\Reminder;
use Symfony\Contracts\EventDispatcher\Event;

class ScheduleReminderEvent extends Event
{
    public const NAME = 'schedule.reminder';

    protected Reminder $reminder;

    /**
     * @param Reminder $reminder
     */
    public function __construct(Reminder $reminder)
    {
        $this->reminder = $reminder;
    }

    /**
     * @return Reminder
     */
    public function getReminder(): Reminder
    {
        return $this->reminder;
    }

    /**
     * @param Reminder $reminder
     */
    public function setReminder(Reminder $reminder): void
    {
        $this->reminder = $reminder;
    }

}