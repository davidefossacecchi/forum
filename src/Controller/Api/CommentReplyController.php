<?php

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Entity\CommentReply;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('comments/{comment}/replies')]
class CommentReplyController extends AbstractFOSRestController
{
    use ChecksFormRequests;
    public function __construct(private readonly EntityManagerInterface $em)
    {

    }

    #[Rest\Post('', name: 'add_comment_reply')]
    public function create(Request $request, Comment $comment, #[CurrentUser] User $user): View
    {
        $form = $this->createFormBuilder(new CommentReply())
            ->add('text', TextType::class)
            ->getForm();

        $form->submit($request->request->all());

        $this->throwExceptionIfInvalid($form);

        /** @var CommentReply $commentReply */
        $commentReply = $form->getData();

        $commentReply->setComment($comment)
            ->setAuthor($user);

        $this->em->persist($commentReply);
        $this->em->flush();
        $this->em->refresh($commentReply);
        return $this->view($commentReply);

    }
}
