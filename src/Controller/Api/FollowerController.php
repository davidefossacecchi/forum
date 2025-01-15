<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class FollowerController extends AbstractFOSRestController
{
    #[Rest\Post('/{following}/follower', name: 'follow_user', requirements: ['following' => '\d+'])]
    public function follow(#[CurrentUser] User $user, User $following, EntityManagerInterface $entityManager): View
    {
        $following->addFollower($user);
        $entityManager->persist($following);
        $entityManager->flush();
        return $this->view(['user' => $user]);
    }

    #[Rest\Get('/followed/involved', name: 'followed_involved')]
    public function followedInvolved(
        #[CurrentUser] User $user,
        TopicRepository $topicRepository
    )
    {
        return $this->view(['activity' => $topicRepository->getFollowedInvolvedTopics($user)]);
    }
}
