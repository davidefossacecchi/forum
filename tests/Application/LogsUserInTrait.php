<?php

namespace App\Tests\Application;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\Container;

trait LogsUserInTrait
{
    protected KernelBrowser $client;
    protected Container $container;

    protected function logDefaultUserIn(): void
    {
        $user = $this->getDefaultUser();
        $this->client->loginUser($user);
    }

    protected function getDefaultUser(): User
    {
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $userRepo = $em->getRepository(User::class);
        /** @var User $user */
        return $userRepo->findOneBy(['email' => UserFixtures::KNOWN_EMAIL]);
    }
}