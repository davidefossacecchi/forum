<?php

namespace App\Tests\Unit\Follower;

use App\Entity\Comment;
use App\Entity\CommentReply;
use App\Entity\Topic;
use App\Entity\User;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FollowerTest extends KernelTestCase
{
    public function testFollowers(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        /** @var TopicRepository $topicRepository */
        $topicRepository = $em->getRepository(Topic::class);
        $userRepository = $em->getRepository(User::class);
        $users = $userRepository->findAll();
        /** @var User $user */
        $user = $users[array_rand($users)];
        $followed = $user->getFollowed();
        $followedMap = [];

        // I create a check map from id to 1
        /** @var User $f */
        foreach ($followed as $f) {
            $followedMap[$f->getId()] = 1;
        }

        $followedInvolvedTopics = $topicRepository->getFollowedInvolvedTopics($user);

        $correct = true;

        /** @var Topic $topic */
        foreach ($followedInvolvedTopics as $topic) {
            // break the loop as soon as the check becomes false
            if ($correct === false) {
                break;
            }
            $author = $topic->getAuthor();
            if (false === empty($followedMap[$author->getId()])) {
                continue;
            }

            $comments = $topic->getComments();
            $found = false;
            /** @var Comment $comment */
            foreach ($comments as $comment) {
                if ($found === true) {
                    break;
                }
                $author = $comment->getAuthor();
                if (false === empty($followedMap[$author->getId()])) {
                    $found = true;
                } else {
                    $replies = $comment->getReplies();
                    /** @var CommentReply $reply */
                    foreach ($replies as $reply) {
                        $author = $reply->getAuthor();
                        if (false === empty($followedMap[$author->getId()])) {
                            $found = true;
                            break;
                        }
                    }
                }
            }

            $correct = $correct && $found;
        }

        $this->assertTrue($correct);
    }
}
