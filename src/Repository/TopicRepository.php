<?php

namespace App\Repository;
use App\Entity\Topic;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class TopicRepository extends ServiceEntityRepository
{
    private const PAGE_LENGTH = 25;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Topic::class);
    }

    public function getPage(int $page): Paginator
    {
        $qb = $this->createQueryBuilder('t')
            ->innerJoin('t.user', 'u');
        $offset = self::PAGE_LENGTH * ($page - 1);
        $qb->setFirstResult($offset)
            ->setMaxResults(self::PAGE_LENGTH);
        return new Paginator($qb);
    }

    public function getByUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.user = :author')
            ->setParameter('author', $user)
            ->getQuery()
            ->getResult();
    }

    public function getUserInvolvedTopics(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.comments', 'c')
            ->join('c.replies', 'r')
            ->where('t.user = :user')
            ->orWhere('c.author = :user')
            ->orWhere('r.author = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
