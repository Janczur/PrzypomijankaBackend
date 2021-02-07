<?php

namespace App\Modules\Remembrall\Command;

use App\Modules\Remembrall\Event\SchedulePreReminderEvent;
use App\Modules\Remembrall\Repository\ReminderRepositoryInterface;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ScheduleTodaysPreRemindersCommand extends Command
{
    protected static $defaultName = 'remembrall:schedule-pre-reminders';

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
        $this->setDescription('It schedules all pre reminders that should be send today');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $today = new DateTime('today');
        $tomorrow = new DateTime('tomorrow');
        $reminders = $this->reminderRepository->getAllPreRemindersToBeSendBetween($today, $tomorrow);
        foreach ($reminders as $reminder) {
            $event = new SchedulePreReminderEvent($reminder);
            $this->eventDispatcher->dispatch($event, SchedulePreReminderEvent::NAME);
        }
        $io->success('Zlecono zaplanowanie wysyłki ' . count($reminders) . ' przed przypomnień');
        return Command::SUCCESS;
    }
}
