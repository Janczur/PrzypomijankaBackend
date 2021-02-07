<?php

namespace App\Modules\Remembrall\Handler;

use App\Modules\Remembrall\Message\SendPreReminderSms;
use App\Modules\Remembrall\Repository\ReminderRepositoryInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendPreReminderSmsHandler implements MessageHandlerInterface, LoggerAwareInterface
{
    private ReminderRepositoryInterface $reminderRepository;
    private LoggerInterface $logger;

    public function __construct(ReminderRepositoryInterface $reminderRepository)
    {
        $this->reminderRepository = $reminderRepository;
    }

    public function __invoke(SendPreReminderSms $message)
    {
        $id = $message->getReminder()->getId();
        if (!$reminder = $this->reminderRepository->find($id)) {
            $this->logger->error(
                'System nie mógł zaplanować wysłania przed przypomnienia, ponieważ przypomnienie o ID: {id} nie istnieje.',
                ['id' => $id]
            );
            return;
        }
        $reminder->setRemindAt($message->getReminder()->getRemindAt());
        // @TODO implement SMS Sender
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
