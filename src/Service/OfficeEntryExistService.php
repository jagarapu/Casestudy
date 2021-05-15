<?php

namespace App\Service;

use App\Entity\OfficeOccupancy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OfficeEntryExistService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * OfficeEntryExistService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param $office
     * @throws \Exception
     */
    public function entryOffice($office)
    {
        $officeOccupancy = new OfficeOccupancy();
        $user = $this->tokenStorage->getToken()->getUser();
        $officeOccupancy->setUser($user);
        $officeOccupancy->setOffice($office);
        $officeOccupancy->setEntryTime(new \DateTime());
        $officeOccupancy->setExitTime(null);
        $officeOccupancy->setStatus(1);
        $this->entityManager->persist($officeOccupancy);
        $this->entityManager->flush();
    }

    /**
     * @param OfficeOccupancy $officeOccupancy
     * @throws \Exception
     */
    public function exitOffice(OfficeOccupancy $officeOccupancy)
    {
        $officeOccupancy->setExitTime(new \DateTime());
        $officeOccupancy->setStatus(2);
        $this->entityManager->persist($officeOccupancy);
        $this->entityManager->flush();
    }
}