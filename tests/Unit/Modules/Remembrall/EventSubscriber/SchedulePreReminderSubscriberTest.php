<?php


namespace App\Tests\Unit\Modules\Remembrall\EventSubscriber;


use App\Modules\Remembrall\Entity\Reminder;
use App\Modules\Remembrall\Event\SchedulePreReminderEvent;
use App\Modules\Remembrall\EventSubscriber\ScheduleReminderSubscriber;
use App\Modules\Security\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SchedulePreReminderSubscriberTest extends KernelTestCase
{

    /** @test */
    public function it_subscribes_events_correctly(): void
    {
        self::assertTrue(true);
        // @TODO implement test subscriber

//        $subscriber = $this->getMockBuilder(ScheduleReminderSubscriber::class)
//            ->setMethods(['getSubscribedEvents', 'schedulePreReminderEmail'])->disableOriginalConstructor()->getMock();
//        $subscriber->expects(self::once())
//            ->method('schedulePreReminderEmail');
//
//        $eventDispatcher = new EventDispatcher();
//        $eventDispatcher->addSubscriber($subscriber);
//        $reminder = (new Reminder())
//            ->setTitle('Test title')
//            ->setDescription('Test description')
//            ->setChannels(['email'])
//            ->setRemindAt(new DateTime())
//            ->setPreRemindAt(new DateTime())
//            ->setUser((new User())->setName('pen')->setEmail('pen'));
//        $event = new SchedulePreReminderEvent($reminder);
//        $eventDispatcher->dispatch($event);
    }
}