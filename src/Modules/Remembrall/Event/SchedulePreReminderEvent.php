<?php


namespace App\Modules\Remembrall\Event;


use App\Modules\Remembrall\Entity\PreReminder;
use Symfony\Contracts\EventDispatcher\Event;

class SchedulePreReminderEvent extends Event
{
    public const NAME = 'schedule.pre_reminder';

    protected PreReminder $preReminder;

    public function __construct(PreReminder $preReminder)
    {
        $this->preReminder = $preReminder;
    }

    public function getPreReminder(): PreReminder
    {
        return $this->preReminder;
    }

    public function setPreReminder(PreReminder $preReminder): void
    {
        $this->preReminder = $preReminder;
    }

}