<?php

namespace App\Service;

use App\Entity\Office;
use App\Entity\OfficeOccupancy;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class OfficeEntryExistService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * OfficeEntryExistService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     * @param Office $office
     * @return OfficeOccupancy
     * @throws \Exception
     */
    public function entryEmployeeOffice(User $user, Office $office)
    {
        $officeOccupancy = new OfficeOccupancy();
        $officeOccupancy->setUser($user);
        $officeOccupancy->setOffice($office);
        $officeOccupancy->setEntryTime(new \DateTime());
        $officeOccupancy->setExitTime(null);
        $officeOccupancy->setStatus(1); # status 1 means entry to the office
        $this->entityManager->persist($officeOccupancy);
        $this->entityManager->flush();

        return $officeOccupancy;
    }

    /**
     * @param OfficeOccupancy $officeOccupancy
     * @throws \Exception
     */
    public function exitOffice(OfficeOccupancy $officeOccupancy)
    {
        $officeOccupancy->setExitTime(new \DateTime());
        $officeOccupancy->setStatus(2); # status 2 means exit office
        $this->entityManager->persist($officeOccupancy);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     * @return bool
     */
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