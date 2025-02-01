<?php

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Entity\Topic;
use App\Entity\User;
use App\Forms\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[Route('topics/{topic}/comments', requirements: ['topic' => '\d+'])]
class CommentController extends AbstractFOSRestController
{
    use ChecksFormRequests;
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[Rest\Get('', name: 'get_comments')]
    #[OA\Response(
        response: 200,
        description: 'Returns the comments of a topic',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Comment::class))
        )
    )]
    public function index(Topic $topic)
    {
        /** @var CommentRepository $commentsRepo */
        $commentsRepo = $this->em->getRepository(Comment::class);
        $comments = $commentsRepo->findByTopicId($topic->getId());
        return $this->view($comments);
    }

    #[Rest\Post("", name: 'add_comment')]
    #[OA\Response(
        response: 200,
        description: 'Add a comment to the topic',
        content: new Model(type: Comment::class)
    )]
    #[OA\RequestBody(content: new Model(type: CommentType::class))]
    public function create(Request $request, Topic $topic, #[CurrentUser] User $user): View
    {
        $form = $this->createForm(CommentType::class, new Comment());

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

    #[Rest\Delete('/{comment}', name: 'delete_comment', requirements: ['comment' => '\d+'])]
    #[IsGranted('delete', 'comment')]
    #[OA\Response(
        response: 200,
        description: 'Deletes the selected comment'
    )]
    public function delete(Comment $comment): void
    {
        $this->em->remove($comment);
        $this->em->flush();
    }
}
