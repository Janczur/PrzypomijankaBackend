<?php


namespace App\Modules\Remembrall\EventSubscriber;


use App\Modules\Remembrall\Entity\Reminder;
use App\Modules\Remembrall\Event\SchedulePreReminderEvent;
use App\Modules\Remembrall\Event\ScheduleReminderEvent;
use App\Modules\Remembrall\Message\SendPreReminderEmail;
use App\Modules\Remembrall\Message\SendPreReminderSms;
use App\Modules\Remembrall\Message\SendReminderEmail;
use App\Modules\Remembrall\Message\SendReminderSms;
use App\Modules\Remembrall\Utils\ReminderTimeCalculatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class ScheduleReminderSubscriber implements EventSubscriberInterface
{
    private MessageBusInterface $bus;
    private ReminderTimeCalculatorInterface $reminderTimeCalculator;

    public function __construct(MessageBusInterface $bus, ReminderTimeCalculatorInterface $reminderTimeCalculator)
    {
        $this->bus = $bus;
        $this->reminderTimeCalculator = $reminderTimeCalculator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SchedulePreReminderEvent::NAME => [
                ['schedulePreReminderEmail', 10],
                ['schedulePreReminderSms', 0],
            ],
            ScheduleReminderEvent::NAME => [
                ['scheduleReminderEmail', 10],
                ['scheduleReminderSms', 0],
            ]
        ];
    }

    public function schedulePreReminderEmail(SchedulePreReminderEvent $event): void
    {
        $reminder = $event->getReminder();
        if (!$this->supports(Reminder::EMAIL_CHANNEL, $reminder->getChannels())) {
            return;
        }
        $message = new SendPreReminderEmail($reminder);
        $delay = $this->reminderTimeCalculator->getRemainingMillisecondsUntil($reminder->getPreRemindAt());
        $this->bus->dispatch($message, [
            new DelayStamp($delay)
        ]);
    }

    private function supports(string $channel, array $channels): bool
    {
        return in_array($channel, $channels, true);
    }

    public function schedulePreReminderSms(SchedulePreReminderEvent $event): void
    {
        $reminder = $event->getReminder();
        if (!$this->supports(Reminder::SMS_CHANNEL, $reminder->getChannels())) {
            return;
        }
        $message = new SendPreReminderSms($reminder);
        $delay = $this->reminderTimeCalculator->getRemainingMillisecondsUntil($reminder->getPreRemindAt());
        $this->bus->dispatch($message, [
            new DelayStamp($delay)
        ]);
    }

    public function scheduleReminderEmail(ScheduleReminderEvent $event): void
    {
        $reminder = $event->getReminder();
        if (!$this->supports(Reminder::EMAIL_CHANNEL, $reminder->getChannels())) {
            return;
        }
        $message = new SendReminderEmail($reminder);
        $delay = $this->reminderTimeCalculator->getRemainingMillisecondsUntil($reminder->getRemindAt());
        $this->bus->dispatch($message, [
            new DelayStamp($delay)
        ]);
    }

    public function scheduleReminderSms(ScheduleReminderEvent $event): void
    {
        $reminder = $event->getReminder();
        if (!$this->supports(Reminder::SMS_CHANNEL, $reminder->getChannels())) {
            return;
        }
        $message = new SendReminderSms($reminder);
        $delay = $this->reminderTimeCalculator->getRemainingMillisecondsUntil($reminder->getRemindAt());
        $this->bus->dispatch($message, [
            new DelayStamp($delay)
        ]);
    }
}