<?php


namespace App\Tests\Unit\Modules\Remembrall\Handler;


use App\DataFixtures\Modules\Remembrall\Entity\ReminderFixtures;
use App\Modules\Remembrall\Entity\Reminder;
use App\Modules\Remembrall\Handler\SendReminderEmailHandler;
use App\Modules\Remembrall\Message\SendReminderEmail;
use App\Modules\Remembrall\Utils\ReminderCalculator;
use DateInterval;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;

class SendReminderEmailHandlerTest extends KernelTestCase
{
    use FixturesTrait;

    private SendReminderEmailHandler $handler;
    private MailerInterface $mailer;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $em = $kernel->getContainer()->get('doctrine')->getManager();
        $reminderRepository = $em->getRepository(Reminder::class);
        $this->mailer = $this->createMock(MailerInterface::class);
        $reminderCalculator = new ReminderCalculator();
        $this->handler = new SendReminderEmailHandler(
            $reminderRepository,
            $this->mailer,
            $em,
            $reminderCalculator
        );

        $logger = $this->createMock(LoggerInterface::class);
        $this->handler->setLogger($logger);
    }

    /** @test */
    public function it_correctly_sends_reminder_via_email(): void
    {
        /** @var Reminder $reminder */
        $reminder = $this->loadFixtures([ReminderFixtures::class])
            ->getReferenceRepository()
            ->getReference(ReminderFixtures::getReferenceKey(0));
        $message = new SendReminderEmail($reminder);
        $handler = $this->handler;

        $this->mailer->expects(self::once())->method('send');
        $handler($message);
    }
}