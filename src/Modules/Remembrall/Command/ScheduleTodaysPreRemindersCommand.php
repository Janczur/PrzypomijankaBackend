<?php

namespace App\Modules\Remembrall\Command;

use App\Modules\Remembrall\Event\SchedulePreReminderEvent;
use App\Modules\Remembrall\Repository\PreReminderRepositoryInterface;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ScheduleTodaysPreRemindersCommand extends Command
{
    protected static $defaultName = 'remembrall:schedule-pre-reminders';
    protected static string $defaultDescription = 'It schedules all pre reminders that should be send today';

    private PreReminderRepositoryInterface $repository;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(PreReminderRepositoryInterface $repository, EventDispatcherInterface $eventDispatcher)
    {
        $this->repository = $repository;
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

        $reminders = $this->repository->getAllPreRemindersToBeSendBetween(new DateTime('today'), new DateTime('tomorrow'));
        if (($remindersCount = count($reminders)) < 1) {
            $io->info('Brak przed przypomnień do zaplanowania wysyłki');
            return Command::SUCCESS;
        }
        foreach ($reminders as $reminder) {
            $event = new SchedulePreReminderEvent($reminder);
            $this->eventDispatcher->dispatch($event, SchedulePreReminderEvent::NAME);
        }
        $io->success('Zlecono zaplanowanie wysyłki ' . $remindersCount . ' przed przypomnień');
        return Command::SUCCESS;
    }
}
