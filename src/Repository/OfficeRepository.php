<?php

namespace App\Repository;

use App\Entity\Office;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Office|null find($id, $lockMode = null, $lockVersion = null)
 * @method Office|null findOneBy(array $criteria, array $orderBy = null)
 * @method Office[]    findAll()
 * @method Office[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfficeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Office::class);
    }

    /**
     * @return array
     */
    public function findAllCities()
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->select('DISTINCT(o.city) as city')
            ->where('o.isEnabled = 1');

        $result = $queryBuilder->getQuery()->getResult();

        return array_column($result, 'city');
    }

    /**
     * @param $officeSearch
     * @return mixed
     */
    public function officeSearchByCity($officeSearch)
    {
        $queryBuilder = $this->createQueryBuilder('o');
        if($officeSearch->getCity()){
            $queryBuilder->andWhere('o.city = :city')
                ->setParameter('city', $officeSearch->getCity());
        }

        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }
}
