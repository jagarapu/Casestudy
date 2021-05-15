<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var  EncoderFactoryInterface
     */
    private $encoderFactory;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * UserManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface $router
     * @param EncoderFactoryInterface $encoderFactory
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router,
                                EncoderFactoryInterface $encoderFactory, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->encoderFactory = $encoderFactory;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Call the function after setting the RawPassword to the user
     *
     * @param User $user
     */
    public function manageUpdatedPassword(User $user)
    {
        /** @var User $user */
        $rawPassword = $user->getRawPassword();
        $encoder = $this->encoderFactory->getEncoder($user);
        $user->encodePassword($encoder);
        $user->setRawPassword($rawPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

}