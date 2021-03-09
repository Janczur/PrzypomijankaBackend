<?php

namespace App\Modules\Remembrall\Command;

use App\Modules\Remembrall\Repository\ReminderRepositoryInterface;
use App\Modules\Remembrall\Utils\ReminderCalculator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CalculateNextRemindersDatesSentYesterdayCommand extends Command
{
    protected static $defaultName = 'remembrall:calculate-next-reminders-dates';
    protected static string $defaultDescription = 'It calculates the date of the next cyclic reminders that were sent yesterday';

    private ReminderRepositoryInterface $repository;
    private ReminderCalculator $calculator;
    private EntityManagerInterface $em;

    public function __construct(ReminderRepositoryInterface $repository, ReminderCalculator $calculator, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->calculator = $calculator;
        $this->em = $em;
        parent::__construct();
    }


    protected function configure(): void
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $reminders = $this->repository->getAllCyclicRemindersToBeSendBetween(new DateTime('yesterday'), new DateTime('today'));
        if (($remindersCount = count($reminders)) < 1){
            $io->info('Brak cyklicznych przypomnień, które powinny mieć obliczoną datę następnego przypomnienia');
            return Command::SUCCESS;
        }
        foreach ($reminders as $reminder) {
            $reminder->setRemindAt($this->calculator->calculateNextReminderDate($reminder));
            if ($reminder->hasPreReminder()) {
                $reminder->getPreReminder()->setRemindAt($this->calculator->calculateNextPreReminderDate($reminder));
            }
            $this->em->persist($reminder);
        }
        $this->em->flush();

        $io->success('Poprawnie obliczono i zapisano daty ' . $remindersCount . ' następnych przypomnień i ich przed przypomnień.');
        return Command::SUCCESS;
    }
}
