<?php


namespace App\Tests\Unit\Modules\Remembrall\Command;


use App\DataFixtures\Modules\Remembrall\Entity\ReminderFixtures;
use App\Modules\Remembrall\Command\ScheduleTodaysPreRemindersCommand;
use App\Modules\Remembrall\Entity\Reminder;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ScheduleTodaysPreRemindersCommandTest extends KernelTestCase
{
    use FixturesTrait;

    /** @test */
    public function it_dispatches_schedule_pre_reminder_events_correctly(): void
    {
        $this->loadFixtures([ReminderFixtures::class]);
        $kernel = static::bootKernel();
        $application = new Application($kernel);

        $em = $kernel->getContainer()->get('doctrine')->getManager();
        $reminderRepository = $em->getRepository(Reminder::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $schedulePreRemindersCommand = new ScheduleTodaysPreRemindersCommand($reminderRepository, $dispatcher);
        $application->add($schedulePreRemindersCommand);

        $command = $application->find('remembrall:schedule-pre-reminders');
        $commandTester = new CommandTester($command);

        $dispatcher->expects(self::once())->method('dispatch');
        $commandTester->execute([]);
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('[OK] Zlecono zaplanowanie wysyłki 1 przed przypomnień', $output);
    }
}