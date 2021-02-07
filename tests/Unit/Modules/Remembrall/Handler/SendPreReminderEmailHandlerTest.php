<?php


namespace App\Tests\Unit\Modules\Remembrall\Handler;


use App\DataFixtures\Modules\Remembrall\Entity\ReminderFixtures;
use App\Modules\Remembrall\Entity\Reminder;
use App\Modules\Remembrall\Handler\SendPreReminderEmailHandler;
use App\Modules\Remembrall\Message\SendPreReminderEmail;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;

class SendPreReminderEmailHandlerTest extends KernelTestCase
{
    use FixturesTrait;

    /** @var LoggerInterface|MockObject */
    private $logger;

    /** @var SendPreReminderEmailHandler */
    private SendPreReminderEmailHandler $handler;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $reminderRepository = $em->getRepository(Reminder::class);

        $mailer = $this->getMockBuilder(MailerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->handler = new SendPreReminderEmailHandler(
            $reminderRepository,
            $mailer,
            $em
        );
        $this->handler->setLogger($logger);
    }

    /** @test */
    public function it_marks_pre_reminder_email_as_sent_after_successful_send(): void
    {
        /** @var Reminder $reminderFixture */
        $reminderFixture = $this->loadFixtures([ReminderFixtures::class])
            ->getReferenceRepository()
            ->getReference(ReminderFixtures::getReferenceKey(0));
        $message = new SendPreReminderEmail($reminderFixture);
        $handler = $this->handler;

        self::assertFalse($reminderFixture->getPreReminded());
        $handler($message);
        self::assertTrue($reminderFixture->getPreReminded());
    }
}