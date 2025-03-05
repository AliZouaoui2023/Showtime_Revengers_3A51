<?php

namespace App\Repository;

use App\Entity\Cour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Cour>
 */
class CourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cour::class);
    }

    /**
     * Find distinct course types
     * 
     * @return array List of unique course types
     */
    public function findDistinctTypes(): array 
    {
        return $this->createQueryBuilder('c')
            ->select('DISTINCT c.typeCour')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all courses sorted by start and end dates
     * 
     * @return array List of courses sorted by date
     */
    public function findAllSorted(): array 
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.dateDebut', 'ASC')
            ->addOrderBy('c.dateFin', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Count courses by their type
     * 
     * @return array Course type counts
     */
    public function countCoursByType(): array 
    {
        return $this->createQueryBuilder('c')
            ->select('c.typeCour AS typeCour, COUNT(c.id) AS count')
            ->groupBy('c.typeCour')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find courses by a specific field
     * 
     * @param mixed $value Search value
     * @return array List of matching courses
     */
    public function findByExampleField($value): array 
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find a single course by a specific field
     * 
     * @param mixed $value Search value
     * @return Cour|null Matching course or null
     */
    public function findOneBySomeField($value): ?Cour 
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find courses by specific type
     * 
     * @param string $type Course type
     * @return array List of courses of the specified type
     */
    public function findByType(string $type): array 
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.typeCour = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find courses within a specific date range
     * 
     * @param \DateTime $startDate Start date
     * @param \DateTime $endDate End date
     * @return array List of courses within the date range
     */
    public function findBetweenDates(\DateTime $startDate, \DateTime $endDate): array 
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.dateDebut >= :start')
            ->andWhere('c.dateFin <= :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getResult();
    }

    /**
     * Custom query to find courses with optional filtering
     * 
     * @param array $filters Optional filters
     * @return QueryBuilder Prepared query builder
     */
    public function findWithFilters(array $filters = []): QueryBuilder 
    {
        $qb = $this->createQueryBuilder('c');

        if (isset($filters['type'])) {
            $qb->andWhere('c.typeCour = :type')
               ->setParameter('type', $filters['type']);
        }

        if (isset($filters['minCost'])) {
            $qb->andWhere('c.cout >= :minCost')
               ->setParameter('minCost', $filters['minCost']);
        }

        if (isset($filters['maxCost'])) {
            $qb->andWhere('c.cout <= :maxCost')
               ->setParameter('maxCost', $filters['maxCost']);
        }

        return $qb;
    }
}