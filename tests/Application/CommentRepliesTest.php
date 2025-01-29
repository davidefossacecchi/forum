<?php

namespace App\Tests\Application;

use App\Entity\CommentReply;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class CommentRepliesTest extends AbstractApplicationTest
{
    use LogsUserInTrait;
    use UrlGeneratorTrait;
    use UseCommentsRepo;

    public function testAddCommentReply(): void
    {
        $testContent = '123456';
        $comment = $this->getTestComment();
        $url = $this->generateUrl('add_comment_reply', ['comment' => $comment->getId()]);
        $this->logDefaultUserIn();
        $this->client->jsonRequest('POST', $url, ['text' => $testContent]);
        $this->assertResponseIsSuccessful();

        $reply = $this->getDecodedResponse();
        $this->assertEquals($testContent, $reply['text']);
        $this->assertEquals($this->getDefaultUser()->getId(), $reply['author']['id']);

        $replyId = (int) $reply['id'];

        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        /** @var ServiceEntityRepository $replyRepo */
        $replyRepo = $em->getRepository(CommentReply::class);
        /** @var CommentReply $persistedReply */
        $persistedReply = $replyRepo->find($replyId);

        $this->assertNotEmpty($persistedReply);
        $this->assertEquals($testContent, $persistedReply->getText());
    }

    public function testDeleteCommentReply(): void
    {
        $user = $this->getDefaultUser();

        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        /** @var ServiceEntityRepository $commentsReplyRepo */
        $commentsReplyRepo = $em->getRepository(CommentReply::class);

        /** @var CommentReply|null $commentReply */
        $commentReply = $commentsReplyRepo->createQueryBuilder('cr')
            ->where('cr.author = :author')
            ->setMaxResults(1)
            ->setParameter('author', $user)
            ->getQuery()
            ->getOneOrNullResult();
        $commentReplyId = $commentReply->getId();
        if (is_null($commentReply)) {
            $this->markTestSkipped('No available comment reply');
        }

        $comment = $commentReply->getComment();

        $url = $this->generateUrl('delete_reply', ['comment' => $comment->getId(), 'reply' => $commentReply->getId()]);
        $this->logDefaultUserIn();

        $this->client->jsonRequest('DELETE', $url);
        $this->assertResponseIsSuccessful();

        $persistedCommentReply = $commentsReplyRepo->find($commentReplyId);
        $this->assertNull($persistedCommentReply);
    }

    public function testOnlyOwnerCanDeleteCommentReply(): void
    {
        $user = $this->getDefaultUser();
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);

        /** @var ServiceEntityRepository $commentReplyRepo */
        $commentReplyRepo = $em->getRepository(CommentReply::class);

        /** @var CommentReply|null $commentReply */
        $commentReply = $commentReplyRepo->createQueryBuilder('cr')
            ->where('cr.author != :user')
            ->setMaxResults(1)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();

        if (empty($commentReply)) {
            $this->markTestSkipped('No valid comment reply found');
        }

        $this->client->loginUser($user);
        $url = $this->generateUrl('delete_reply', ['comment' => $commentReply->getComment()->getId(), 'reply' => $commentReply->getId()]);
        $this->client->jsonRequest('DELETE', $url);
        $this->assertResponseStatusCodeSame(403);
    }
}