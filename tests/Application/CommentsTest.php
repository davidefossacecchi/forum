<?php

namespace App\Tests\Application;

use App\Entity\Comment;
use App\Entity\Topic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class CommentsTest extends AbstractApplicationTest
{
    use LogsUserInTrait;
    use UrlGeneratorTrait;

    public function testList(): void
    {
        $commentRef = $this->getTestComment();

        $path = $this->generateUrl('get_comments', ['topic' => $commentRef->getTopic()->getId()]);
        $this->logDefaultUserIn();
        $this->client->jsonRequest('GET', $path);
        $this->assertResponseIsSuccessful();

        $comments = $this->getDecodedResponse();
        $found = false;
        foreach ($comments as $comment) {
            $this->assertNotEmpty($comment['id']);
            $this->assertNotEmpty($comment['text']);
            $this->assertNotEmpty($comment['author']);
            if ($comment['id'] === $commentRef->getId()) {
                $found = true;
            }
        }

        $this->assertTrue($found);
    }

    public function testAddComment(): void
    {
        $commentText = 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...';
        $url = $this->generateUrl('add_comment', ['topic' => $this->getTestTopic()->getId()]);
        $this->logDefaultUserIn();

        $this->client->jsonRequest('POST', $url, ['text' => $commentText]);
        $this->assertResponseIsSuccessful();

        $response = $this->getDecodedResponse();

        $this->assertNotEmpty($response['id']);
        $this->assertEquals($commentText, $response['text']);

        $commentRepository = $this->getCommentsRepository();

        $commentEntity = $commentRepository->find($response['id']);

        $this->assertNotEmpty($commentEntity);
    }

    private function getTestComment(): Comment
    {

        $all = $this->getCommentsRepository()->findAll();
        /** @var Comment $commentRef */
        return $all[0];
    }

    private function getTestTopic(): Topic
    {
        return $this->getTestComment()->getTopic();
    }

    private function getCommentsRepository(): ServiceEntityRepository
    {
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        return $em->getRepository(Comment::class);
    }
}