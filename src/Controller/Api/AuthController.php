<?php

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Entity\Topic;
use App\Entity\User;
use App\Forms\SignupType;
use App\Repository\CommentRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use OpenApi\Attributes as OA;

class AuthController extends AbstractFOSRestController
{
    use ChecksFormRequests;
    #[Rest\Post(path: '/signup', name: 'signup')]
    #[OA\Response(
        response: 201,
        description: "User correctly created"
    )]
    #[OA\RequestBody(content: new Model(type: SignupType::class))]
    public function signup(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): ?View
    {
        $form = $this->createForm(SignupType::class, new User());

        $form->submit($request->request->all());

        $this->throwExceptionIfInvalid($form);

        /** @var User $user */
        $user = $form->getData();

        $password = $user->getPlainPassword();
        $cryptedPassword = $hasher->hashPassword($user, $password);
        $user->setPassword($cryptedPassword);

        $em->persist($user);
        $em->flush();
        return null;
    }


    #[Rest\Get(path: '/me', name: 'me')]
    #[OA\Response(
        response: 200,
        description: "Returns the current user data",
        content: new Model(type: User::class)
    )]
    public function me(#[CurrentUser] User $user): View
    {
        return $this->view([
            'email' => $user->getEmail(),
            'name' => $user->getName()
        ]);
    }

    #[Rest\Get(path: '/me/topics', name: 'my_topics')]
    #[OA\Response(
        response: 200,
        description: "Returns the topics created by the user",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Topic::class))
        )
    )]
    public function userTopics(#[CurrentUser] User $user, TopicRepository $topicRepository): View
    {
        return $this->view([
            'topics' => $topicRepository->getByUser($user)
        ]);
    }

    #[Rest\Get(path: '/me/comments', name: 'my_comments')]
    #[OA\Response(
        response: 200,
        description: "Returns the comments created by the user",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Comment::class))
        )
    )]
    public function userComments(#[CurrentUser] User $user, CommentRepository $commentRepository): View
    {
        return $this->view([
            'comments' => $commentRepository->getUserComments($user)
        ]);
    }

    #[Rest\Get(path: '/me/involved', name: 'involved_topics')]
    #[OA\Response(
        response: 200,
        description: "Returns the topics where the user is involved",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Topic::class))
        )
    )]
    public function userInvolvedTopics(#[CurrentUser] User $user, TopicRepository $topicRepository): View
    {
        return $this->view([
            'involved' => $topicRepository->getUserInvolvedTopics($user)
        ]);
    }
}
