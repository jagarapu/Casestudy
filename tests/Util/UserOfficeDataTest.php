<?php

namespace App\Tests\Util;

use App\Entity\Office;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class UserOfficeDataTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private $token = null;
    private $tokenStorage = null;
    /**
     * {@inheritDoc}
     */
    protected function setUp():void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->clearUserData();
        $this->tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->disableOriginalConstructor()->getMock();
        $this->tokenStorage->method('getToken')->willReturn($this->token);
        $this->createAuthorizeUser();
        $this->logIn();
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function clearUserData()
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $offices = $this->entityManager->getRepository(Office::class)->findAll();
        foreach ($users as $user) {
            $this->entityManager->remove($user);
        }
        foreach ($offices as $office) {
            $this->entityManager->remove($office);
        }
        $this->entityManager->flush();
    }

    /**
     * This function will test user data
     */
    public function testUserData()
    {
        $user = self::$kernel->getContainer()->get('doctrine')
            ->getRepository(User::class)->findOneBy(['username' => 'Techmuser']);

        $this->assertEquals($user->getUsername(), "Techmuser");
        $this->assertEquals($user->getFirstName(), "Techmuser");
        $this->assertEquals($user->getLastName(), "Techm");
        $this->assertEquals($user->getEmail(), "techmusertest@gmail.com");
        $this->assertEquals($user->getEmployeeId(), "Tch100");

    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testOfficeData()
    {
        // Office Object
        $office = new Office();
        $office->setTitle('Tech Mahindra');
        $office->setCity('Bangalore');
        $office->setCountry('India');
        $office->setAddress('Electronic City Phase II');
        $office->setPostCode(560100);
        $office->setPhoneNumber(958796686);
        $office->setState('Karnataka ');
        $office->setOfficeCapacity(5);
        $office->setIsEnabled(true);

        $this->entityManager->persist($office);
        $this->entityManager->flush();

        $this->assertEquals($office->getTitle(), "Tech Mahindra");
        $this->assertEquals($office->getCity(), "Bangalore");
        $this->assertEquals($office->getCountry(), "India");
        $this->assertEquals($office->getAddress(), "Electronic City Phase II");
        $this->assertEquals($office->getPostCode(), "560100");
    }

    /**
     * @return User
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createAuthorizeUser()
    {
        // User Object
        $user = new User();
        $user->setUsername("Techmuser");
        $user->setFirstName('Techmuser');
        $user->setLastName('Techm');
        $user->setEmail('techmusertest@gmail.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setIsEnabled(true);
        $user->setEmployeeId("Tch100");

        $user->setPassword('Password@123');
        $password = $user->generatePassword();
        $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);

        $encoders = [
            User::class => $defaultEncoder,
        ];
        $encoderFactory = new EncoderFactory($encoders);
        $encoder = $encoderFactory->getEncoder($user);
        $user->encodePassword($encoder);
        $user->setRawPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function logIn()
    {
        $user = self::$kernel->getContainer()->get('doctrine')
            ->getRepository(User::class)->findOneBy(['username' => 'Techmuser']);
        $token = new UsernamePasswordToken($user, 'Password@123', 'db_provider', ['ROLE_ADMIN']);
        self::$kernel->getContainer()->get('security.token_storage')->setToken($token);
        $this->token = $token;
    }

}