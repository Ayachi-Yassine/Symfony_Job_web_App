<?php

namespace App\Repository;

use App\Entity\Job;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Job>
 *
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    /**
     * Find active jobs ordered by creation date
     */
    public function findActiveJobs()
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('j.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find jobs by category
     */
    public function findByCategory($categoryId)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.category = :category')
            ->andWhere('j.isActive = :active')
            ->setParameter('category', $categoryId)
            ->setParameter('active', true)
            ->orderBy('j.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Search jobs by title or company
     */
    public function search($searchTerm)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.title LIKE :term OR j.company LIKE :term')
            ->andWhere('j.isActive = :active')
            ->setParameter('term', '%' . $searchTerm . '%')
            ->setParameter('active', true)
            ->orderBy('j.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
