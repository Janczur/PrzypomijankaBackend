<?php


namespace App\Modules\Remembrall\EventSubscriber;


use App\Modules\Remembrall\Entity\Reminder;
use App\Modules\Remembrall\Event\SchedulePreReminderEvent;
use App\Modules\Remembrall\Event\ScheduleReminderEvent;
use App\Modules\Remembrall\Message\SendPreReminderEmail;
use App\Modules\Remembrall\Message\SendPreReminderSms;
use App\Modules\Remembrall\Message\SendReminderEmail;
use App\Modules\Remembrall\Message\SendReminderSms;
use App\Modules\Remembrall\Utils\ReminderCalculator;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class ScheduleReminderSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    private MessageBusInterface $bus;
    private ReminderCalculator $reminderCalculator;
    private LoggerInterface $logger;

    public function __construct(MessageBusInterface $bus, ReminderCalculator $reminderCalculator)
    {
        $this->bus = $bus;
        $this->reminderCalculator = $reminderCalculator;
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
        $preReminder = $event->getPreReminder();
        if (!$reminder = $preReminder->getReminder()) {
            $this->logger->error(
                'System próbował wysłać wiadomość email Przed Przypomnienia Przypomnienia, które nie istnieje. ID Przed Przypomnienia: {id}',
                ['id' => $preReminder->getId()]
            );
            return;
        }
        if (!$this->supports(Reminder::EMAIL_CHANNEL, $reminder->getChannels())) {
            return;
        }
        $message = new SendPreReminderEmail($reminder);
        $delay = $this->reminderCalculator->getRemainingMillisecondsUntil($preReminder->getRemindAt());
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
        $preReminder = $event->getPreReminder();
        if (!$reminder = $preReminder->getReminder()) {
            $this->logger->error(
                'System próbował wysłać wiadomość SMS Przed Przypomnienie Przypomnienia, które nie istnieje. ID Przed Przypomnienia: {id}',
                ['id' => $preReminder->getId()]
            );
            return;
        }
        if (!$this->supports(Reminder::SMS_CHANNEL, $reminder->getChannels())) {
            return;
        }
        $message = new SendPreReminderSms($reminder);
        $delay = $this->reminderCalculator->getRemainingMillisecondsUntil($preReminder->getRemindAt());
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
        $delay = $this->reminderCalculator->getRemainingMillisecondsUntil($reminder->getRemindAt());
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
        $delay = $this->reminderCalculator->getRemainingMillisecondsUntil($reminder->getRemindAt());
        $this->bus->dispatch($message, [
            new DelayStamp($delay)
        ]);
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}