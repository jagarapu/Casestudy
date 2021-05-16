<?php


namespace App\Service;


use App\Entity\Office;
use App\Entity\OfficeOccupancy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OfficeCapacityCheck
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
     * @param Office $office
     * @return bool
     */
    public function checkOfficeCapacity(Office $office)
    {
        $officeWiseOccupancy = $this->entityManager->getRepository(OfficeOccupancy::class)
                                                    ->fetchOfficeOccupancyData();

        if (isset($officeWiseOccupancy[$office->getTitle()]) === $office->getOfficeCapacity()) {
            return false;
        }
        return true;
    }
}