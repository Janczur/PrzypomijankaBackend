<?php


namespace App\Tests\Feature;


use App\Modules\Security\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{

    /** @test */
    public function registered_user_can_login(): void
    {
        $user = $this->getTestUser();
        $client = self::createClient();
        $client->request('POST', '/auth/register', [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword()
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $registerResponse = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('success', $registerResponse['message']);
        self::assertEquals($user->getName(), $registerResponse['user']['name']);
        self::assertEquals($user->getEmail(), $registerResponse['user']['email']);
        self::assertArrayNotHasKey('password', $registerResponse['user']);

        $client->request('POST', '/auth/login', [
            'email' => $user->getEmail(),
            'password' => $user->getPassword()
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $loginResponse = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('success', $loginResponse['message']);
        self::assertEquals($user->getName(), $loginResponse['user']['name']);
        self::assertEquals($user->getEmail(), $loginResponse['user']['email']);
        self::assertArrayNotHasKey('password', $loginResponse['user']);
    }

    private function getTestUser(): User
    {
        return (new User())
            ->setName('Test')
            ->setEmail('test@test.pl')
            ->setPassword('secret');
    }

    /** @test */
    public function guest_must_provide_all_necessary_registration_details()
    {
        $client = self::createClient();
        $client->request('POST', '/auth/register');
        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals('error', $response['message']);
        self::assertCount(3, $response['errors']['violations']);
    }

    /** @test */
    public function guest_must_provide_valid_name(): void
    {
        $user = $this->getTestUser();
        $client = self::createClient();
        $client->request('POST', '/auth/register', [
            'name' => 'a',
            'email' => $user->getEmail(),
            'password' => $user->getPassword()
        ]);
        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals('error', $response['message']);
        self::assertEquals('[name]: This value is too short. It should have 3 characters or more.', $response['errors']['detail']);
    }

    /** @test */
    public function guest_must_provide_valid_email(): void
    {
        $user = $this->getTestUser();
        $client = self::createClient();
        $client->request('POST', '/auth/register', [
            'name' => $user->getName(),
            'email' => 'test@test',
            'password' => $user->getPassword()
        ]);
        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals('error', $response['message']);
        self::assertEquals('[email]: This value is not a valid email address.', $response['errors']['detail']);
    }

    /** @test */
    public function guests_cannot_view_homepage(): void
    {
        $client = self::createClient();
        $client->request('GET', '/');
        $response = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(['message' => 'Wymagane uwierzytelnienie'], $response);
    }
}