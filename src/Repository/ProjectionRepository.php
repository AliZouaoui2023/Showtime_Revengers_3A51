<?php

namespace App\Repository;

use App\Entity\Projection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Projection>
 */
class ProjectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Projection::class);
    }


    public function searchProjections(?string $query): array
    {
        if (!$query) {
            return [];
        }
    
        return $this->createQueryBuilder('p')
            ->leftJoin('p.film', 'f')  // Assuming there's a relationship between Projection and Film
            ->where('f.nomFilm LIKE :query')  // Search by film name
            ->orWhere('p.dateProjection LIKE :query')  // Search by projection date
            ->orWhere('p.salle LIKE :query')  // Search by projection room
            ->setParameter('query', '%'.$query.'%')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Projection[] Returns an array of Projection objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Projection
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
