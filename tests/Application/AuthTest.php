<?php

namespace App\Tests\Application;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthTest extends WebTestCase
{
    protected KernelBrowser $client;
    protected Container $container;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->container = static::getContainer();
    }

    public function testLogin(): void
    {
        $this->runLoginTest('dphox@test.com', '12345678');
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
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $userRepo = $em->getRepository(User::class);
        /** @var User $user */
        $user = $userRepo->findOneBy(['email' => 'dphox@test.com']);
        $this->client->loginUser($user);

        $path = $this->generateUrl('me');
        $this->client->jsonRequest('GET', $path);
        $this->assertResponseIsSuccessful();

        $responseContent = $this->client->getResponse()->getContent();
        $response = json_decode($responseContent, true);
        $this->assertTrue((bool) $response);
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

    private function generateUrl(string $name, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        /** @var UrlGeneratorInterface $router */
        $router = $this->container->get(UrlGeneratorInterface::class);
        return $router->generate($name, $parameters, $referenceType);
    }
}