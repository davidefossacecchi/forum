<?php

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Entity\CommentReply;
use App\Entity\User;
use App\Forms\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Route('comments/{comment}/replies', requirements: ['comment' => '\d+'])]
class CommentReplyController extends AbstractFOSRestController
{
    use ChecksFormRequests;

    public function __construct(private readonly EntityManagerInterface $em)
    {

    }

    #[Rest\Post('', name: 'add_comment_reply')]
    #[OA\Response(
        response: 200,
        description: 'Add a comment reply to a comment',
        content: new Model(type: CommentReply::class)
    )]
    #[OA\RequestBody(
        content: new Model(type: CommentType::class)
    )]
    public function create(Request $request, Comment $comment, #[CurrentUser] User $user): View
    {
        $form = $this->createForm(CommentType::class, new CommentReply());

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

    #[Rest\Delete('/{reply}', name: 'delete_reply')]
    #[IsGranted('delete', 'reply')]
    #[OA\Response(
        response: 200,
        description: 'Deletes a comment reply'
    )]
    public function delete(CommentReply $reply): void
    {
        $this->em->remove($reply);
        $this->em->flush();
    }
}
