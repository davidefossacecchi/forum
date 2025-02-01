<?php

namespace App\Controller\Api;

use App\DTO\ActivityDto;
use App\Entity\User;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use OpenApi\Attributes as OA;

class FollowerController extends AbstractFOSRestController
{
    #[Rest\Post('/{following}/follower', name: 'follow_user', requirements: ['following' => '\d+'])]
    #[OA\Response(
        response: 200,
        description: 'Adds the current user as follower of the specified user',
        content: new Model(type: User::class)
    )]
    public function follow(#[CurrentUser] User $user, User $following, EntityManagerInterface $entityManager): View
    {
        if ($following->getId() === $user->getId()) {
            return $this->view(['message' => 'You cannot follow yourself'], Response::HTTP_BAD_REQUEST);
        }
        $following->addFollower($user);
        $entityManager->persist($following);
        $entityManager->flush();
        return $this->view($user);
    }

    #[Rest\Get('/followed/involved', name: 'followed_involved')]
    #[OA\Response(
        response: 200,
        description: 'Returns the activity of the followed users',
        content: new Model(type: ActivityDto::class)

    )]
    public function followedInvolved(
        #[CurrentUser] User $user,
        TopicRepository $topicRepository
    ): View
    {
        return $this->view(new ActivityDto($topicRepository->getFollowedInvolvedTopics($user)));
    }
}
