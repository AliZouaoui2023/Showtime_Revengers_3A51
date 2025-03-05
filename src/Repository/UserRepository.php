<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
// src/Repository/UserRepository.php
public function countUsersByRole(): array
{
    return $this->createQueryBuilder('u')
        ->select('u.role, COUNT(u.id) as count')
        ->groupBy('u.role')
        ->getQuery()
        ->getResult(\Doctrine\ORM\Query::HYDRATE_SCALAR);
}

public function findByCriteria(?string $name, ?string $email, ?string $role): array
{
    $qb = $this->createQueryBuilder('u');

    if ($name) {
        $qb->andWhere('u.Nom LIKE :name')
           ->setParameter('name', '%' . $name . '%');
    }

    if ($email) {
        $qb->andWhere('u.email LIKE :email')
           ->setParameter('email', '%' . $email . '%');
    }

    if ($role) {
        $qb->andWhere('u.role = :role')
           ->setParameter('role', $role);
    }

    return $qb->getQuery()->getResult();
}

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
