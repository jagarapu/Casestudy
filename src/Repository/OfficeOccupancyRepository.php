<?php

namespace App\Repository;

use App\Entity\Office;
use App\Entity\OfficeOccupancy;
use App\Entity\User;
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
    public function findOfficeOccupancyStatus(User $user, Office $office)
    {
        $qb = $this->createQueryBuilder('oc');
        $qb->select("oc.status as status")
            ->where('oc.office = :officeId')
            ->andWhere('oc.user = :userId')
            ->andWhere('oc.exitTime IS NULL')
            ->setParameter('officeId', $office->getId())
            ->setParameter('userId', $user->getId())
        ;

        $result =  array_column($qb->getQuery()
            ->getResult(), 'status');

        return $result;
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
            ->where('oc.status = :status')
            ->setParameter('status', 1)
            ->groupBy('oc.office');

        $result =  array_column($qb->getQuery()
            ->getResult(), 'users_count', 'office_title');

        return $result;
    }

    public function getOffice(User $user)
    {
        $qb = $this->createQueryBuilder('oc')
            ->select('o.id')
            ->leftJoin('oc.office', 'o')
            ->leftJoin('oc.user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId())
            ->andWhere('oc.exitTime Is NULL')
        ;

        $result =  array_column($qb->getQuery()
            ->getResult(), 'id');

        return $result;
    }

    public function getUserAlreadyOccupiedOfficeStatus($user)
    {
        $qb = $this->createQueryBuilder('oc');
        $qb->select("oc")
            ->where('oc.user = :userId')
            ->andWhere('oc.status = :status')
            ->setParameter('userId', $user->getId())
            ->setParameter('status', 1)
        ;

        return $qb->getQuery()->getResult();
    }

    public function findOccupiedOffice(User $user)
    {
        $qb = $this->createQueryBuilder('oc')
            ->select('oc.id')
            ->leftJoin('oc.user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId())
            ->andWhere('oc.exitTime Is NULL')
        ;

        $result =  array_column($qb->getQuery()
            ->getResult(), 'id');

        return $result;
    }
}
