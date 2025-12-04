<?php

namespace App\Repository;

use App\Entity\UserActivity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserActivity>
 *
 * @method UserActivity|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserActivity|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserActivity[]    findAll()
 * @method UserActivity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserActivity::class);
    }

    public function findByUser($user, $limit = 50)
    {
        return $this->createQueryBuilder('ua')
            ->andWhere('ua.user = :user')
            ->setParameter('user', $user)
            ->orderBy('ua.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByAction($action)
    {
        return $this->createQueryBuilder('ua')
            ->andWhere('ua.action = :action')
            ->setParameter('action', $action)
            ->orderBy('ua.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findRecentActivityByUser($user, $limit = 10)
    {
        return $this->createQueryBuilder('ua')
            ->andWhere('ua.user = :user')
            ->setParameter('user', $user)
            ->orderBy('ua.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countLoginsByUser($user, \DateTime $from, \DateTime $to)
    {
        return $this->createQueryBuilder('ua')
            ->select('COUNT(ua.id)')
            ->andWhere('ua.user = :user')
            ->andWhere('ua.action = :action')
            ->andWhere('ua.createdAt >= :from')
            ->andWhere('ua.createdAt <= :to')
            ->setParameter('user', $user)
            ->setParameter('action', 'login')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
