<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Topic;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TopicFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixture::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('it_IT');
        $userRepository = $manager->getRepository(User::class);
        $users = $userRepository->findAll();

        $categories = $manager->getRepository(Category::class)->findAll();

        foreach ($users as $user) {
            $categoryIndex = array_rand($categories);
            $category = $categories[$categoryIndex];
            $topic = new Topic();
            $topic->setTitle($faker->realText(100))
                ->setText($faker->realText(500))
                ->setUser($user)
                ->setCategory($category);

            $manager->persist($topic);
        }

        $manager->flush();
    }
}
