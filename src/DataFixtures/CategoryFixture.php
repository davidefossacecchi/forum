<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
class CategoryFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $factory = Factory::create('it_IT');

        for ($i = 0; $i <= 20; $i++) {
            $name = $factory->realText(20);

            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
        }

        $manager->flush();

    }
}
