<?php

namespace App\Tests\Application;

use App\DataFixtures\UserFixtures;

class AuthTest extends AbstractApplicationTest
{
    use LogsUserInTrait;
    use UrlGeneratorTrait;

    public function testLogin(): void
    {
        $this->runLoginTest(UserFixtures::KNOWN_EMAIL, UserFixtures::KNOWN_PASSWORD);
    }

    public function testSignup(): void
    {
        $path = $this->generateUrl('signup');
        $email = 'qwertypointioaopnzsfsoijshalkjfdsalqhi@gmail.com';
        $plainPassword = '12345678';
        $name = 'John';

        $this->client->jsonRequest('POST', $path, compact('email', 'plainPassword', 'name'));
        $this->assertResponseIsSuccessful();

        $this->runLoginTest($email, $plainPassword);
    }

    public function testMe(): void
    {
        $this->logDefaultUserIn();
        $path = $this->generateUrl('me');
        $this->client->jsonRequest('GET', $path);
        $this->assertResponseIsSuccessful();

        $response = $this->getDecodedResponse();
        $this->assertNotEmpty($response['email']);
        $this->assertNotEmpty($response['name']);
    }

    private function runLoginTest(string $email, string $password): void
    {
        $this->client->jsonRequest('POST','/api/login_check', compact('email', 'password'));
        $this->assertResponseIsSuccessful();


        $responseContent = $this->client->getResponse()->getContent();
        $response = json_decode($responseContent, true);
        $this->assertTrue((bool) $response);
        $this->assertNotEmpty($response['token']);
        $this->assertNotEmpty($response['refresh_token']);
    }
}