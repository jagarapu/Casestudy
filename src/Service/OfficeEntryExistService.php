<?php

namespace App\Service;

use App\Entity\Office;
use App\Entity\OfficeOccupancy;
use App\Entity\User;
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
    public function entryEmployeeOffice(Office $office)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $officeOccupancy = new OfficeOccupancy();
        $officeOccupancy->setUser($user);
        $officeOccupancy->setOffice($office);
        $officeOccupancy->setEntryTime(new \DateTime());
        $officeOccupancy->setExitTime(null);
        $officeOccupancy->setStatus(1);
        $this->entityManager->persist($officeOccupancy);
        $this->entityManager->flush();
        dump($officeOccupancy);

        return $officeOccupancy;
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

    public function checkAlreadyEnterToOffice(User $user)
    {
        $officeOccupancy = $this->entityManager->getRepository(OfficeOccupancy::class)
            ->getUserAlreadyOccupiedOfficeStatus($user);
        if ($officeOccupancy) {
            return false;
        }
        return true;
    }
}