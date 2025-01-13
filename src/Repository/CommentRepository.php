<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }


    public function findByTopicId(int $topicId)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.replies', 'r')
            ->where('IDENTITY(c.topic) = :topicId')
            ->setParameter('topicId', $topicId)
            ->getQuery()
            ->getResult();
    }

    public function getUserComments(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.author = :author')
            ->setParameter('author', $user)
            ->getQuery()
            ->getResult();
    }

}
