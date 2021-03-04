<?php

namespace App\Modules\Remembrall\Handler;

use App\Modules\Remembrall\Message\SendReminderEmail;
use App\Modules\Remembrall\Repository\ReminderRepositoryInterface;
use App\Modules\Remembrall\Utils\ReminderCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendReminderEmailHandler implements MessageHandlerInterface, LoggerAwareInterface
{

    private ReminderRepositoryInterface $reminderRepository;
    private LoggerInterface $logger;
    private MailerInterface $mailer;
    private EntityManagerInterface $em;
    private ReminderCalculator $reminderCalculator;

    public function __construct(
        ReminderRepositoryInterface $reminderRepository,
        MailerInterface $mailer,
        EntityManagerInterface $em,
        ReminderCalculator $reminderCalculator
    )
    {
        $this->reminderRepository = $reminderRepository;
        $this->mailer = $mailer;
        $this->em = $em;
        $this->reminderCalculator = $reminderCalculator;
    }


    public function __invoke(SendReminderEmail $message)
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

        $email = (new TemplatedEmail())
            ->to($reminder->getOwner()->getUsername())
            ->subject($reminder->getTitle())
            ->htmlTemplate('Remembrall/emails/ReminderTemplate.html.twig')
            ->context(['description' => $reminder->getDescription()]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->critical(
                "System nie mógł wysłać wiadomości Email dla przypomnienia o ID: {id}\nBłąd: {exception}",
                ['id' => $reminder->getId(), 'exception' => $e->getMessage()]
            );
            return;
        }

        // @todo wyciągnąć obliczanie następnego przypomnienia poza ten handler - to nie jego odpowiedzialność
        $nextRemindAt = $this->reminderCalculator->calculateNextReminderDate($reminder);
        $reminder->setRemindAt($nextRemindAt);
        $this->em->persist($reminder);
        $this->em->flush();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
