<?php

namespace App\Tests\Util;

use App\Entity\Office;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class DataFixtureTestCase extends WebTestCase
{
    /** @var  Application $application */
    protected static $application;

    /** @var  Client $client */
    protected $client;

    /** @var  EntityManager $entityManager */
    protected $entityManager;

    /**
     * {@inheritDoc}
     */
    public function setUp():void
    {
        self::runCommand('doctrine:database:drop --force');
        self::runCommand('doctrine:database:create');
        self::runCommand('doctrine:schema:create');

        $this->client = static::createClient();

        $container = $this->client->getContainer();

        $this->entityManager = $container->get('doctrine.orm.entity_manager');

        parent::setUp();
    }

    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    /**
     * @return User
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createUser()
    {
        // User Object
        $user = new User();
        $user->setUsername("TechmAdmin");
        $user->setFirstName('Techm Contact');
        $user->setLastName('Techm');
        $user->setEmail('contact@gmail.com');
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

    /**
     * @return Office
     */
    public function createOffice()
    {
        // Office Object
        $office = new Office();
        $office->setTitle('Tech Mahindra');
        $office->setCity('Bangalore');
        $office->setCountry('India');
        $office->setAddress('Plot No. 45- 47 KIADB Industrial Area, Electronic City Phase II');
        $office->setPostCode(560100);
        $office->setPhoneNumber(958796686);
        $office->setState('Karnataka ');
        $office->setOfficeCapacity(5);
        $office->setIsEnabled(true);

        return $office;
    }

}