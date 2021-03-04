<?php


namespace App\Tests\Feature\Modules\Remembrall\Controller;


use App\DataFixtures\Modules\Security\Entity\UserFixtures;
use DateTime;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReminderControllerTest extends WebTestCase
{
    use FixturesTrait;

    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = self::createClient();
    }

    /**
     * @test
     * @dataProvider reminderUrls
     */
    public function guests_cannot_manage_reminders(string $method, string $url): void
    {
        $this->client->request($method, $url);
        self::assertEquals(401, $this->client->getResponse()->getStatusCode());
        self::assertEquals('{"message":"Wymagane uwierzytelnienie"}', $this->client->getResponse()->getContent());
    }

    public function reminderUrls(): array
    {
        return [
            ['GET', '/reminders'],
            ['GET', '/reminders/1'],
            ['POST', '/reminders/save'],
            ['PUT', '/reminders/1/update'],
            ['DELETE', '/reminders/1/delete'],
        ];
    }

    /** @test */
    public function user_can_create_reminder(): void
    {
        $this->loginAsTestUser();

        $oneTimeReminder = $this->getOneTimeReminderData();
        $responseBody = $this->saveReminderRequest($oneTimeReminder);
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/json');
        self::assertEquals($oneTimeReminder['title'], $responseBody['title']);
        self::assertEquals($oneTimeReminder['description'], $responseBody['description']);
        self::assertEquals($oneTimeReminder['channels'], $responseBody['channels']);
        self::assertEquals($oneTimeReminder['cyclic'], $responseBody['cyclic']);
        self::assertEquals($oneTimeReminder['pre_reminder'], $responseBody['pre_reminder']);
        self::assertEquals($oneTimeReminder['remind_at'], $responseBody['remind_at']);
        self::assertTrue($responseBody['active']);
    }

    private function loginAsTestUser(): void
    {
        $userFixtures = $this->loadFixtures([
            UserFixtures::class,
        ])->getReferenceRepository();
        $user = $userFixtures->getReference(UserFixtures::USER_REFERENCE);
        $this->client->loginUser($user);
    }

    private function getOneTimeReminderData(): array
    {
        return [
            'title' => 'Test title',
            'description' => 'Test description',
            'cyclic' => null,
            'pre_reminder' => null,
            'channels' => ['email', 'sms'],
            'remind_at' => $this->getValidRemindAtDate()
        ];
    }

    private function getValidRemindAtDate(): string
    {
        return (new DateTime('now + 2 days'))->format('Y-m-d H:i:s');
    }

    /** @test */
    public function a_reminder_requires_valid_title(): void
    {
        $this->loginAsTestUser();
        $reminderData = [
            'title' => '',
        ];
        $responseBody = $this->saveReminderRequest($reminderData);
        self::assertResponseStatusCodeSame(400);
        self::assertEquals('title: This value is too short. It should have 3 characters or more.', $responseBody['detail']);
    }

    private function saveReminderRequest(array $reminderData): array
    {
        $this->client->request('POST', 'reminders/save', [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($reminderData)
        );
        return json_decode($this->client->getResponse()->getContent(), true);
    }

    /** @test */
    public function a_reminder_requires_valid_description(): void
    {
        $this->loginAsTestUser();
        $reminderData = [
            'description' => 'as',
        ];
        $responseBody = $this->saveReminderRequest($reminderData);
        self::assertResponseStatusCodeSame(400);
        self::assertEquals('description: This value is too short. It should have 3 characters or more.', $responseBody['detail']);
    }

    /** @test */
    public function a_reminder_requires_supported_channels(): void
    {
        $this->loginAsTestUser();
        $reminderData = [
            'channels' => ['email', 'slack'],
        ];
        $responseBody = $this->saveReminderRequest($reminderData);
        self::assertResponseStatusCodeSame(400);
        self::assertEquals('channels: One or more of the given values is invalid.', $responseBody['detail']);
    }

    /** @test */
    public function a_reminder_requires_valid_remind_at_date(): void
    {
        $this->loginAsTestUser();
        $reminderData = [
            'remind_at' => (new DateTime())->format('Y-m-d H:i:s')
        ];
        $responseBody = $this->saveReminderRequest($reminderData);
        self::assertResponseStatusCodeSame(400);
        self::assertCount(1, $responseBody['violations']);
    }

    /** @test */
    public function a_pre_reminder_days_before_must_be_valid(): void
    {
        $this->loginAsTestUser();
        $reminderData = [
            'cyclic' => [],
            'pre_reminder' => [
                'days_before' => 0
            ],
        ];
        $responseBody = $this->saveReminderRequest($reminderData);
        self::assertResponseStatusCodeSame(400);
        self::assertEquals('days_before: This value should be positive.', $responseBody['detail']);
    }

    /** @test */
    public function a_reminder_cyclic_periodicity_must_be_valid(): void
    {
        $this->loginAsTestUser();
        $reminderData = [
            'cyclic' => [
                'periodicity' => -2
            ],
            'pre_reminder' => []
        ];
        $responseBody = $this->saveReminderRequest($reminderData);
        self::assertResponseStatusCodeSame(400);
        self::assertEquals('periodicity: This value should be positive.', $responseBody['detail']);
    }

    /** @test */
    public function a_reminder_cyclic_type_must_be_valid(): void
    {
        $this->loginAsTestUser();
        $reminderData = [
            'cyclic' => [
                'type_id' => 0
            ],
        ];
        $responseBody = $this->saveReminderRequest($reminderData);
        self::assertResponseStatusCodeSame(400);
        self::assertEquals('type_id: The value you selected is not a valid choice.', $responseBody['detail']);
    }

}