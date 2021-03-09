<?php

namespace App\Modules\Remembrall\Command;

use App\Modules\Remembrall\Event\ScheduleReminderEvent;
use App\Modules\Remembrall\Repository\ReminderRepositoryInterface;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ScheduleTodaysRemindersCommand extends Command
{
    protected static $defaultName = 'remembrall:schedule-reminders';
    protected static string $defaultDescription = 'It schedules all reminders that should be send today';

    private ReminderRepositoryInterface $reminderRepository;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(ReminderRepositoryInterface $reminderRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->reminderRepository = $reminderRepository;
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct();
    }


    protected function configure(): void
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $reminders = $this->reminderRepository->getAllRemindersToBeSendBetween(new DateTime('today'), new DateTime('tomorrow'));
        if (($remindersCount = count($reminders)) < 1) {
            $io->info('Brak przypomnień do zaplanowania wysyłki');
            return Command::SUCCESS;
        }
        foreach ($reminders as $reminder) {
            $event = new ScheduleReminderEvent($reminder);
            $this->eventDispatcher->dispatch($event, ScheduleReminderEvent::NAME);
        }
        $io->success('Zlecono zaplanowanie wysyłki ' . $remindersCount . ' przypomnień');
        return Command::SUCCESS;
    }
}
