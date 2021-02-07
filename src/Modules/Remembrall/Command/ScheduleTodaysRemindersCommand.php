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

    private ReminderRepositoryInterface $reminderRepository;

    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param ReminderRepositoryInterface $reminderRepository
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(ReminderRepositoryInterface $reminderRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->reminderRepository = $reminderRepository;
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct();
    }


    protected function configure(): void
    {
        $this->setDescription('It schedules all reminders that should be send today');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $today = new DateTime('today');
        $tomorrow = new DateTime('tomorrow');
        $reminders = $this->reminderRepository->getAllRemindersToBeSendBetween($today, $tomorrow);
        foreach ($reminders as $reminder) {
            $event = new ScheduleReminderEvent($reminder);
            $this->eventDispatcher->dispatch($event, ScheduleReminderEvent::NAME);
        }
        $io->success('Zlecono zaplanowanie wysyłki ' . count($reminders) . ' przypomnień');
        return Command::SUCCESS;
    }
}
