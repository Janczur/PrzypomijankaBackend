<?php


namespace App\Tests\Unit\Modules\Remembrall\EventSubscriber;


use App\DataFixtures\Modules\Remembrall\Entity\ReminderFixtures;
use App\Modules\Remembrall\Entity\Reminder;
use App\Modules\Remembrall\Event\SchedulePreReminderEvent;
use App\Modules\Remembrall\Event\ScheduleReminderEvent;
use App\Modules\Remembrall\EventSubscriber\ScheduleReminderSubscriber;
use App\Modules\Remembrall\Message\SendPreReminderEmail;
use App\Modules\Remembrall\Message\SendPreReminderSms;
use App\Modules\Remembrall\Message\SendReminderEmail;
use App\Modules\Remembrall\Message\SendReminderSms;
use App\Modules\Remembrall\Utils\ReminderTimeCalculatorInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class SchedulePreReminderSubscriberTest extends KernelTestCase
{
    use FixturesTrait;

    private MessageBusInterface $messageBus;
    private EventDispatcher $dispatcher;
    private Reminder $reminder;

    public function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->dispatcher = new EventDispatcher();
        $this->reminder = $this->loadFixtures([ReminderFixtures::class])
            ->getReferenceRepository()
            ->getReference(ReminderFixtures::getReferenceKey(0));

        $reminderTimeCalculator = $this->createMock(ReminderTimeCalculatorInterface::class);
        $scheduleReminderSubscriber = new ScheduleReminderSubscriber($this->messageBus, $reminderTimeCalculator);
        $this->dispatcher->addSubscriber($scheduleReminderSubscriber);
    }

    /** @test */
    public function subscriber_correctly_dispatches_only_email_reminder_send_message_on_schedule_reminder_event(): void
    {
        $this->reminder->setChannels([Reminder::EMAIL_CHANNEL]);

        $expectedMessage = new SendReminderEmail($this->reminder);
        $this->messageBus->expects(self::once())
            ->method('dispatch')
            ->willReturn(new Envelope($expectedMessage));

        $event = new ScheduleReminderEvent($this->reminder);
        $this->dispatcher->dispatch($event, $event::NAME);
    }

    /** @test */
    public function subscriber_correctly_dispatches_only_sms_reminder_send_message_on_schedule_reminder_event(): void
    {
        $this->reminder->setChannels([Reminder::SMS_CHANNEL]);

        $expectedMessage = new SendReminderSms($this->reminder);
        $this->messageBus->expects(self::once())
            ->method('dispatch')
            ->willReturn(new Envelope($expectedMessage));

        $event = new ScheduleReminderEvent($this->reminder);
        $this->dispatcher->dispatch($event, $event::NAME);
    }

    /** @test */
    public function subscriber_correctly_dispatches_email_and_sms_reminder_send_message_on_schedule_reminder_event(): void
    {
        $this->reminder->setChannels([Reminder::EMAIL_CHANNEL, Reminder::SMS_CHANNEL]);

        $expectedEmailMessage = new SendReminderEmail($this->reminder);
        $expectedSmsMessage = new SendReminderSms($this->reminder);

        $this->messageBus->expects(self::exactly(2))
            ->method('dispatch')
            ->will(self::onConsecutiveCalls(
                new Envelope($expectedSmsMessage),
                new Envelope($expectedEmailMessage)
            ));

        $event = new ScheduleReminderEvent($this->reminder);
        $this->dispatcher->dispatch($event, $event::NAME);
    }

    /** @test */
    public function subscriber_correctly_dispatches_only_email_pre_reminder_send_message_on_schedule_reminder_event(): void
    {
        $this->reminder->setChannels([Reminder::EMAIL_CHANNEL]);

        $expectedMessage = new SendPreReminderEmail($this->reminder);
        $this->messageBus->expects(self::once())
            ->method('dispatch')
            ->willReturn(new Envelope($expectedMessage));

        $event = new SchedulePreReminderEvent($this->reminder);
        $this->dispatcher->dispatch($event, $event::NAME);
    }

    /** @test */
    public function subscriber_correctly_dispatches_only_sms_pre_reminder_send_message_on_schedule_reminder_event(): void
    {
        $this->reminder->setChannels([Reminder::SMS_CHANNEL]);

        $expectedMessage = new SendPreReminderSms($this->reminder);

        $this->messageBus->expects(self::once())
            ->method('dispatch')
            ->willReturn(new Envelope($expectedMessage));

        $event = new SchedulePreReminderEvent($this->reminder);
        $this->dispatcher->dispatch($event, $event::NAME);
    }

    /** @test */
    public function subscriber_correctly_dispatches_email_and_sms_pre_reminder_send_message_on_schedule_reminder_event(): void
    {
        $this->reminder->setChannels([Reminder::EMAIL_CHANNEL, Reminder::SMS_CHANNEL]);

        $expectedEmailMessage = new SendPreReminderEmail($this->reminder);
        $expectedSmsMessage = new SendPreReminderSms($this->reminder);

        $this->messageBus->expects(self::exactly(2))
            ->method('dispatch')
            ->will(self::onConsecutiveCalls(
                new Envelope($expectedSmsMessage),
                new Envelope($expectedEmailMessage)
            ));

        $event = new ScheduleReminderEvent($this->reminder);
        $this->dispatcher->dispatch($event, $event::NAME);
    }
}