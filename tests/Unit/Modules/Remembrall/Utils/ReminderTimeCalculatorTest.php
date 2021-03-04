<?php


namespace App\Tests\Unit\Modules\Remembrall\Utils;


use App\DataFixtures\Modules\Remembrall\Entity\ReminderFixtures;
use App\Modules\Remembrall\Entity\Cyclic;
use App\Modules\Remembrall\Entity\CyclicType;
use App\Modules\Remembrall\Entity\Reminder;
use App\Modules\Remembrall\Utils\ReminderCalculator;
use DateInterval;
use DateTime;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReminderTimeCalculatorTest extends KernelTestCase
{
    use FixturesTrait;

    /** @test */
    public function milliseconds_from_now_until_given_date_time_are_calculated_correctly(): void
    {
        $now = (new DateTime())->getTimestamp();
        $to = new DateTime('+2 day');
        $expectedMilliseconds = ($to->getTimestamp() - $now) * 1000;
        $reminderCalculator = new ReminderCalculator();
        $actualMilliseconds = $reminderCalculator->getRemainingMillisecondsUntil($to);
        self::assertEquals($expectedMilliseconds, $actualMilliseconds);
    }

    /** @test  */
    public function it_calculates_date_of_next_remind_correctly(): void
    {
        /** @var Reminder $reminder */
        $reminder = $this->loadFixtures([ReminderFixtures::class])
            ->getReferenceRepository()
            ->getReference(ReminderFixtures::getReferenceKey(0));
        $cyclic = (new Cyclic())
            ->setPeriodicity(3)
            ->setTypeId(Cyclic::MONTH);
        $reminder->setCyclic($cyclic);
        $expectedNextRemindDate = $reminder->getRemindAt()->add(new DateInterval('P3M'));
        $reminderCalculator = new ReminderCalculator();
        $reminderCalculator->calculateNextReminderDate($reminder);
        self::assertEquals($expectedNextRemindDate, $reminder->getRemindAt());
    }
}