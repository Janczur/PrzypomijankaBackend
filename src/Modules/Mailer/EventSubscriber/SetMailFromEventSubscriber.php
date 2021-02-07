<?php


namespace App\Modules\Mailer\EventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class SetMailFromEventSubscriber implements EventSubscriberInterface
{
    private string $mailFrom;

    public function __construct(string $mailFrom)
    {
        $this->mailFrom = $mailFrom;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MessageEvent::class => 'onMessage',
        ];
    }

    public function onMessage(MessageEvent $event): void
    {
        $email = $event->getMessage();
        if (!$email instanceof Email) {
            return;
        }
        $email->from(new Address($this->mailFrom));
    }
}