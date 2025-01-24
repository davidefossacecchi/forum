<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{

    public const KNOWN_EMAIL = 'dphox@test.com';
    public const KNOWN_PASSWORD = '12345678';

    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {

    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('it_IT');
        /** @var User[] $users */
        $users = [];
        for ($i = 0; $i < 20; $i++) {
            if ($i === 0) {
                $email = self::KNOWN_EMAIL;
                $password = self::KNOWN_PASSWORD;
            } else {
                $email = $faker->email;
                $password = $faker->password;
            }
            $user = new User();
            $user->setEmail($email)
                ->setName($faker->firstName)
                ->setPassword($this->hasher->hashPassword($user, $password));
            $manager->persist($user);
            $users[] = $user;
        }
        $manager->flush();


        foreach ($users as $user) {
            $followedIndexes = array_rand($users, rand(2,5));
            foreach ($followedIndexes as $followedIndex) {
                $followed = $users[$followedIndex];
                if ($followed->getId() != $user->getId()) {
                    $user->addFollowed($followed);
                }
            }
            $manager->persist($user);
        }
        $manager->flush();
    }
}
