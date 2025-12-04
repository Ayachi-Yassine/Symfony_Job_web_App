<?php

namespace App\Repository;

use App\Entity\JobApplication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JobApplication>
 *
 * @method JobApplication|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobApplication|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobApplication[]    findAll()
 * @method JobApplication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobApplication::class);
    }

    public function findByUser($user)
    {
        return $this->createQueryBuilder('ja')
            ->andWhere('ja.user = :user')
            ->setParameter('user', $user)
            ->orderBy('ja.appliedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByJob($job)
    {
        return $this->createQueryBuilder('ja')
            ->andWhere('ja.job = :job')
            ->setParameter('job', $job)
            ->orderBy('ja.appliedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByStatus($status)
    {
        return $this->createQueryBuilder('ja')
            ->andWhere('ja.status = :status')
            ->setParameter('status', $status)
            ->orderBy('ja.appliedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPendingApplications()
    {
        return $this->findByStatus('pending');
    }

    public function countByStatus($status)
    {
        return $this->createQueryBuilder('ja')
            ->select('COUNT(ja.id)')
            ->andWhere('ja.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findUserApplicationForJob($user, $job)
    {
        return $this->createQueryBuilder('ja')
            ->andWhere('ja.user = :user')
            ->andWhere('ja.job = :job')
            ->setParameter('user', $user)
            ->setParameter('job', $job)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
