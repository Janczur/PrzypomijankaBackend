<?php


namespace App\Tests\Unit\Modules\Remembrall\Command;


use App\DataFixtures\Modules\Remembrall\Entity\ReminderFixtures;
use App\Modules\Remembrall\Command\CalculateNextRemindersDatesSentYesterdayCommand;
use App\Modules\Remembrall\Entity\Reminder;
use App\Modules\Remembrall\Utils\ReminderCalculator;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CalculateNextRemindersDatesSentYesterdayCommandTest extends KernelTestCase
{
    use FixturesTrait;

    /** @test */
    public function it_correctly_calculates_and_saves_the_dates_of_the_next_cyclic_reminders_and_their_pre_reminders(): void
    {
        $this->loadFixtures([ReminderFixtures::class]);
        $kernel = static::bootKernel();
        $application = new Application($kernel);

        $em = $kernel->getContainer()->get('doctrine')->getManager();
        $reminderRepository = $em->getRepository(Reminder::class);
        $calculator = new ReminderCalculator();
        $schedulePreRemindersCommand = new CalculateNextRemindersDatesSentYesterdayCommand($reminderRepository, $calculator, $em);
        $application->add($schedulePreRemindersCommand);

        $command = $application->find('remembrall:calculate-next-reminders-dates');
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('[OK] Poprawnie obliczono i zapisano daty 1 następnych przypomnień i ich przed przypomnień.', $output);
    }
}