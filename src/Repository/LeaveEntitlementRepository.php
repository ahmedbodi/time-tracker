<?php

namespace App\Repository;

use App\Entity\LeaveEntitlement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LeaveEntitlement|null find($id, $lockMode = null, $lockVersion = null)
 * @method LeaveEntitlement|null findOneBy(array $criteria, array $orderBy = null)
 * @method LeaveEntitlement[]    findAll()
 * @method LeaveEntitlement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeaveEntitlementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LeaveEntitlement::class);
    }

    // /**
    //  * @return LeaveEntitlement[] Returns an array of LeaveEntitlement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LeaveEntitlement
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
