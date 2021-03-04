<?php


namespace App\Tests\Unit\Modules\Remembrall\Handler;


use App\DataFixtures\Modules\Remembrall\Entity\ReminderFixtures;
use App\Modules\Remembrall\Entity\Reminder;
use App\Modules\Remembrall\Handler\SendPreReminderEmailHandler;
use App\Modules\Remembrall\Message\SendPreReminderEmail;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;

class SendPreReminderEmailHandlerTest extends KernelTestCase
{
    use FixturesTrait;

    private MailerInterface $mailer;
    private SendPreReminderEmailHandler $handler;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $em = $kernel->getContainer()->get('doctrine')->getManager();
        $reminderRepository = $em->getRepository(Reminder::class);
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->handler = new SendPreReminderEmailHandler(
            $reminderRepository,
            $this->mailer
        );

        $logger = $this->createMock(LoggerInterface::class);
        $this->handler->setLogger($logger);
    }

    /** @test */
    public function it_sends_pre_reminder_email_successfully(): void
    {
        /** @var Reminder $reminder */
        $reminder = $this->loadFixtures([ReminderFixtures::class])
            ->getReferenceRepository()
            ->getReference(ReminderFixtures::getReferenceKey(0));
        $message = new SendPreReminderEmail($reminder);
        $handler = $this->handler;

        $this->mailer->expects(self::once())->method('send');
        $handler($message);
    }
}