<?php


namespace App\Tests\Feature\Modules\Security\Controller;


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
        $registerResponseBody = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals($user->getName(), $registerResponseBody['name']);
        self::assertEquals($user->getEmail(), $registerResponseBody['email']);
        self::assertArrayNotHasKey('password', $registerResponseBody);

        $client->request('POST', '/auth/login', [
            'email' => $user->getEmail(),
            'password' => $user->getPassword()
        ]);
        $loginResponseBody = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertEquals($user->getName(), $loginResponseBody['name']);
        self::assertEquals($user->getEmail(), $loginResponseBody['email']);
        self::assertArrayNotHasKey('password', $loginResponseBody);
    }

    private function getTestUser(): User
    {
        return (new User())
            ->setName('User for test')
            ->setEmail('userfortest@testtest.pl')
            ->setPassword('secret');
    }

    /** @test */
    public function guest_must_provide_all_necessary_registration_details()
    {
        $client = self::createClient();
        $client->request('POST', '/auth/register');
        $responseBody = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(400, $client->getResponse()->getStatusCode());
        self::assertCount(3, $responseBody['violations']);
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
        $responseBody = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(400, $client->getResponse()->getStatusCode());
        self::assertEquals('[name]: This value is too short. It should have 3 characters or more.', $responseBody['detail']);
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
        $responseBody = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals(400, $client->getResponse()->getStatusCode());
        self::assertEquals('[email]: This value is not a valid email address.', $responseBody['detail']);
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