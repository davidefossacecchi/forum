<?php

namespace App\Tests\Application;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

trait UseCommentsRepo
{
    protected Container $container;

    protected function getTestComment(): Comment
    {

        $all = $this->getCommentsRepository()->findAll();
        /** @var Comment $commentRef */
        return $all[0];
    }

    protected function getCommentsRepository(): ServiceEntityRepository
    {
        /** @var EntityManagerInterface $em */
        $em = $this->container->get(EntityManagerInterface::class);
        return $em->getRepository(Comment::class);
    }
}