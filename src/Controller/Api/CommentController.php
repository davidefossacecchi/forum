<?php

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Entity\Topic;
use App\Entity\User;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('topics/{topic}/comments')]
class CommentController extends AbstractFOSRestController
{
    use ChecksFormRequests;
    public function __construct(private readonly EntityManagerInterface $em)
    {

    }

    #[Rest\Get('', name: 'get_comments')]
    public function index(Topic $topic)
    {
        /** @var CommentRepository $commentsRepo */
        $commentsRepo = $this->em->getRepository(Comment::class);
        $comments = $commentsRepo->findByTopicId($topic->getId());
        return $this->view($comments);
    }

    #[Rest\Post("", name: 'add_comment')]
    public function create(Request $request, Topic $topic, #[CurrentUser] User $user): View
    {
        $form = $this->createFormBuilder(new Comment())
            ->add('text', TextType::class)
            ->getForm();

        $form->submit($request->request->all());

        $this->throwExceptionIfInvalid($form);

        /** @var Comment $comment */
        $comment = $form->getData();
        $comment->setTopic($topic)
            ->setAuthor($user);

        $this->em->persist($comment);
        $this->em->flush();
        $this->em->refresh($comment);

        return $this->view($comment);
    }
}
