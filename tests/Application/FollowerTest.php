<?php

namespace App\Tests\Application;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class FollowerTest extends AbstractApplicationTest
{
    use LogsUserInTrait;
    use UrlGeneratorTrait;

    public function testFollowerAdd(): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        $userRepo = $em->getRepository(User::class);
        $defaultUser = $this->getDefaultUser();

        /** @var User|null $followingUser */
        $followingUser = $userRepo->createQueryBuilder('u')
            ->where('u.id != :userId')
            ->setParameter('userId', $defaultUser)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (is_null($followingUser)) {
            $this->markTestSkipped('No followable user');
        }

        $url = $this->generateUrl('follow_user', ['following' => $followingUser->getId()]);
        $this->logDefaultUserIn();
        $this->client->jsonRequest('POST', $url);
        $this->assertResponseIsSuccessful();
        $decodedResponse = $this->getDecodedResponse();

        $this->assertNotEmpty($decodedResponse['user']);
    }

    public function testUserCannotFollowHimSelf(): void
    {
        $defaultUser = $this->getDefaultUser();
        $url = $this->generateUrl('follow_user', ['following' => $defaultUser->getId()]);
        $this->logDefaultUserIn();
        $this->client->jsonRequest('POST', $url);
        $this->assertResponseStatusCodeSame(400);
    }

    public function testActivityList(): void
    {
        $this->logDefaultUserIn();
        $url = $this->generateUrl('followed_involved');
        $this->client->jsonRequest('GET', $url);
        $response = $this->getDecodedResponse();

        $this->assertResponseIsSuccessful();
        $this->assertNotEmpty($response['activity']);
    }
}