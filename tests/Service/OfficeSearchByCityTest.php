<?php

namespace App\Tests\Service;

use App\Entity\Office;
use App\Entity\SearchFilter;
use App\Tests\Util\DataFixtureTestCase;

class OfficeSearchByCityTest extends DataFixtureTestCase
{
    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testOfficeSearchByCity()
    {
        $officeSearch = new SearchFilter();
        // Office Object
        // Office Object
        $office = new Office();
        $office->setTitle('Tech Mahindra Pvt');
        $office->setCity('Bangalore');
        $office->setCountry('India');
        $office->setAddress('Plot No. 45- 47 KIADB Industrial Area, Electronic City Phase II');
        $office->setPostCode(560100);
        $office->setPhoneNumber(958796686);
        $office->setState('Karnataka ');
        $office->setOfficeCapacity(5);
        $office->setIsEnabled(true);
        $this->entityManager->persist($office);
        $this->entityManager->flush();
        // Find current office from test DB
        $officeObj = $this->entityManager->getRepository(Office::class)->find($office->getId());
        // set Office Data to Office Search Object
        $officeSearch->setCity($officeObj->getCity());
        // find all offices
        $allOffices = $this->entityManager
            ->getRepository(Office::class)
            ->findAll();
        // search office by city
        $searchOffice = $this->entityManager
            ->getRepository(Office::class)
            ->officeSearchByCity($officeSearch);

        $officesCount = count($allOffices);
        if($allOffices){
            $this->assertCount($officesCount, $allOffices);
        }

        $this->assertEquals($searchOffice[0]->getCity(), "Bangalore");
    }


}