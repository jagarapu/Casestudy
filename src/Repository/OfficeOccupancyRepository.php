<?php

namespace App\Repository;

use App\Entity\Office;
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


    public function findOfficeOccupancyStatus(OfficeOccupancy $officeOccupancy)
    {
        $qb = $this->createQueryBuilder('oc');
        $qb->select("oc.status as status")
            ->leftJoin('oc.office', 'o')
            ->where('o.id = :officeId')
            ->leftJoin('oc.user', 'u')
            ->andWhere('u.id = :userId')
            ->setParameter('officeId', $officeOccupancy->getOffice()->getId())
            ->setParameter('userId', $officeOccupancy->getUser()->getId())
        ;

        return $qb->getQuery()->getResult();
    }

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
