<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\CommentReply;
use App\Entity\Topic;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;use Doctrine\Persistence\ObjectManager;use Faker\Factory;

class CommentReplyFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [CommentFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('it_IT');
        $commentsRepository = $manager->getRepository(Comment::class);
        $usersRepository = $manager->getRepository(User::class);

        $comments = $commentsRepository->findAll();
        $users = $usersRepository->findAll();

        foreach ($comments as $comment) {
            $repliesNum = rand(1, 5);
            $authorsIndex = array_rand($users, $repliesNum);

            if (false === is_array($authorsIndex)) {
                $authorsIndex = [$authorsIndex];
            }

            foreach ($authorsIndex as $authorIndex) {
                $author = $users[$authorIndex];
                $reply = new CommentReply();
                $reply->setComment($comment)
                    ->setText($faker->realText(1000))
                    ->setAuthor($author);

                $manager->persist($reply);
            }
        }

        $manager->flush();
    }

}
