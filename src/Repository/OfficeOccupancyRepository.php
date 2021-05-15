<?php

namespace App\Repository;

use App\Entity\OfficeOccupancy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OfficeOccupancy|null find($id, $lockMode = null, $lockVersion = null)
 * @method OfficeOccupancy|null findOneBy(array $criteria, array $orderBy = null)
 * @method OfficeOccupancy[]    findAll()
 * @method OfficeOccupancy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfficeOccupancyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OfficeOccupancy::class);
    }

    // /**
    //  * @return OfficeOccupancy[] Returns an array of OfficeOccupancy objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OfficeOccupancy
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
