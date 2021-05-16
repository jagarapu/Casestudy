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

    /**
     * @param OfficeOccupancy $officeOccupancy
     * @return mixed
     */
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

    /**
     * @return mixed
     */
    public function fetchOfficeOccupancyData()
    {
        $qb = $this->createQueryBuilder('oc')
            ->leftJoin('oc.office', 'o')
            ->select('o.title as office_title')
            ->addSelect('count(oc.user) as users_count')
            ->groupBy('oc.office');

        $result =  array_column($qb->getQuery()
            ->getResult(), 'users_count', 'office_title');

        return $result;
    }

    public function getOfficeId($user)
    {
        $qb = $this->createQueryBuilder('oc')
            ->where('co.id = :userId')
            ->setParameter('userId', $user->getId());

        return $qb->getQuery()->getResult();
    }
}
