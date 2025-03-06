<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    //    /**
    //     * @return Notification[] Returns an array of Notification objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Notification
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.createdAt', 'DESC') // Tri par date de création décroissante
            ->getQuery()
            ->getResult();
    }
    public function deleteOldNotifications(): void
{
    $twoDaysAgo = new \DateTime('-2 minutes');

    $this->entityManager->createQueryBuilder()
        ->delete(Notification::class, 'n')
        ->where('n.createdAt < :dateLimit')
        ->setParameter('dateLimit', $twoDaysAgo)
        ->getQuery()
        ->execute();
}
   
}
