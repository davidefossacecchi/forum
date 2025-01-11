<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Topic;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;use Doctrine\Persistence\ObjectManager;use Faker\Factory;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [TopicFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('it_IT');
        $topicsRepository = $manager->getRepository(Topic::class);
        $usersRepository = $manager->getRepository(User::class);

        $topics = $topicsRepository->findAll();
        $users = $usersRepository->findAll();

        foreach ($topics as $topic) {
            $commentsNum = rand(1, 5);
            $authorsIndex = array_rand($users, $commentsNum);

            if (false === is_array($authorsIndex)) {
                $authorsIndex = [$authorsIndex];
            }

            foreach ($authorsIndex as $authorIndex) {
                $author = $users[$authorIndex];
                $comment = new Comment();
                $comment->setTopic($topic)
                    ->setText($faker->realText(1000))
                    ->setAuthor($author);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

}
