<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AuthController extends AbstractFOSRestController
{
    use ChecksFormRequests;
    #[Rest\Post(path: '/signup', name: 'signup')]
    public function signup(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): ?View
    {
        $form = $this->createFormBuilder(new User())
            ->add('email', EmailType::class)
            ->add('name', TextType::class)
            ->add('plainPassword', PasswordType::class, ['constraints' => [new Length(min: 8), new NotBlank()]])
            ->getForm();

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
    public function me(#[CurrentUser] User $user): View
    {
        return $this->view([
            'email' => $user->getEmail(),
            'name' => $user->getName()
        ]);
    }

    #[Rest\Get(path: '/me/topics', name: 'my_topics')]
    public function userTopics(#[CurrentUser] User $user, TopicRepository $topicRepository): View
    {
        return $this->view([
            'topics' => $topicRepository->getByUser($user)
        ]);
    }

    #[Rest\Get(path: '/me/comments', name: 'my_comments')]
    public function userComments(#[CurrentUser] User $user, CommentRepository $commentRepository): View
    {
        return $this->view([
            'comments' => $commentRepository->getUserComments($user)
        ]);
    }

    #[Rest\Get(path: '/me/involved', name: 'my_comments')]
    public function userInvolvedTopics(#[CurrentUser] User $user, TopicRepository $topicRepository): View
    {
        return $this->view([
            'involved' => $topicRepository->getUserInvolvedTopics($user)
        ]);
    }
}
