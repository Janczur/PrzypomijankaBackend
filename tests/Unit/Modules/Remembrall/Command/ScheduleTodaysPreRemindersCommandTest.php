<?php


namespace App\Tests\Unit\Modules\Remembrall\Command;


use App\DataFixtures\Modules\Remembrall\Entity\ReminderFixtures;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ScheduleTodaysPreRemindersCommandTest extends KernelTestCase
{
    use FixturesTrait;

    /** @test */
    public function it_schedules_all_todays_pre_reminders_correctly(): void
    {
        $this->loadFixtures([ReminderFixtures::class]);
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('remembrall:schedule-pre-reminders');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('[OK] Zlecono zaplanowanie wysyłki 1 przed przypomnień', $output);
    }
}