<?php

namespace App\Repository;
use App\Entity\Notification;
use App\Entity\Salle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Salle>
 */
class SalleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Salle::class);
    }

//    /**
//     * @return Salle[] Returns an array of Salle objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Salle
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

// src/Repository/SalleRepository.php
public function findById(int $id)
{
    return $this->createQueryBuilder('s')
        ->where('s.idSalle = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult();
}







public function deleteOldNotifications(): void
{
    $twoDaysAgo = new \DateTime('-2 days');

    $this->entityManager->createQueryBuilder()
        ->delete(Notification::class, 'n')
        ->where('n.createdAt < :dateLimit')
        ->setParameter('dateLimit', $twoDaysAgo)
        ->getQuery()
        ->execute();
}


public function findAllOrderedByStatut(string $order = 'ASC')
{
    return $this->createQueryBuilder('s')
        ->orderBy('s.statut', $order) // Trie par statut (ASC ou DESC)
        ->getQuery()
        ->getResult();
}


}
