<?php

namespace App\Tests\Service;

use App\Entity\Office;
use App\Entity\OfficeOccupancy;
use App\Entity\SearchFilter;
use App\Entity\User;
use App\Tests\Util\DataFixtureTestCase;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class OfficeEntrySearchByCityTest extends DataFixtureTestCase
{
    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testOfficeEntry()
    {
        $this->clearUserData();

        // Office Object
        $office = $this->createTestOffice();
        $this->entityManager->persist($office);

        // User Object
        $user = $this->createTestUser();

        $this->entityManager->persist($user);

        // call office entry service
        $this->client = static::createClient();

        $container = $this->client->getContainer();
        // call entry employee service
        $container->get('office_entry_exit')->entryEmployeeOffice($user, $office);

        // Find current office from test DB
        /** @var OfficeOccupancy $officeOccupancyObj */
        $officeOccupancyObj = $this->entityManager->getRepository(OfficeOccupancy::class)->findOneBy(['user' => $user, 'office' => $office]);

        $this->assertEquals($officeOccupancyObj->getUser()->getUsername(), "Testuser");
        $this->assertEquals($officeOccupancyObj->getOffice()->getTitle(), "Test office");
        $this->assertEquals($officeOccupancyObj->getExitTime(), null);
        $this->assertEquals($officeOccupancyObj->getStatus(), 1);

        // Search Filter Object
        $officeSearch = new SearchFilter();

        // Find current office from test DB
        $officeObj = $this->entityManager->getRepository(Office::class)->findOneBy(['title' => "Test office"]);
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

    /**
     * @return Office
     */
    public function createTestOffice()
    {
        $office = new Office();
        $office->setTitle('Test office');
        $office->setCity('Bangalore');
        $office->setCountry('India');
        $office->setAddress('Test address');
        $office->setPostCode(560500);
        $office->setPhoneNumber(89789689989);
        $office->setState('Karnataka');
        $office->setOfficeCapacity(5);
        $office->setIsEnabled(true);

        return $office;
    }

    /**
     * @return User
     */
    public function createTestUser()
    {
        $user = new User();
        $user->setUsername("Testuser");
        $user->setFirstName('Testuser');
        $user->setLastName('Testuser');
        $user->setEmail('testusert@gmail.com');
        $user->setRoles(['ROLE_EMPLOYEE']);
        $user->setIsEnabled(true);
        $user->setEmployeeId("Tchtest105");

        $user->setPassword('Testuser@123');
        $password = $user->generatePassword();
        $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);

        $encoders = [
            User::class => $defaultEncoder,
        ];
        $encoderFactory = new EncoderFactory($encoders);
        $encoder = $encoderFactory->getEncoder($user);
        $user->encodePassword($encoder);
        $user->setRawPassword($password);

        return $user;
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

}