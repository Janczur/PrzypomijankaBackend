<?php


namespace App\Tests\Unit\Modules\Remembrall\Handler;


use App\DataFixtures\Modules\Remembrall\Entity\ReminderFixtures;
use App\Modules\Remembrall\Entity\Reminder;
use App\Modules\Remembrall\Handler\SendReminderEmailHandler;
use App\Modules\Remembrall\Message\SendReminderEmail;
use App\Modules\Remembrall\Utils\ReminderTimeCalculator;
use DateInterval;
use DateTime;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;

class SendReminderEmailHandlerTest extends KernelTestCase
{
    use FixturesTrait;

    /** @var LoggerInterface|MockObject */
    private $logger;

    private SendReminderEmailHandler $handler;

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

        $reminderCalculator = new ReminderTimeCalculator();

        $this->handler = new SendReminderEmailHandler(
            $reminderRepository,
            $mailer,
            $em,
            $reminderCalculator
        );
        $this->handler->setLogger($logger);
    }

    /** @test */
    public function it_correctly_calculates_the_date_of_the_next_reminder(): void
    {
        /** @var Reminder $reminder */
        $reminder = $this->loadFixtures([ReminderFixtures::class])
            ->getReferenceRepository()
            ->getReference(ReminderFixtures::getReferenceKey(0));
        $message = new SendReminderEmail($reminder);
        $handler = $this->handler;

        $expectedNextRemindAt = $reminder->getRemindAt();
        $interval = new DateInterval('P1D');
        $expectedNextRemindAt->add($interval);
        $handler($message);
        self::assertEquals($expectedNextRemindAt->getTimestamp(), $reminder->getRemindAt()->getTimestamp());
    }
}