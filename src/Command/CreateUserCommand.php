<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class CreateUserCommand extends Command
{
    /** @var  Registry */
    private $em;
    private $username;
    private $firstName;
    private $lastName;
    private $email;
    private $roles = [];
    private $allowedRoles = [];

    private $encoderFactory;

    /**
     * CreateUserCommand constructor.
     *
     * @param EntityManagerInterface $em
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(EntityManagerInterface $em, EncoderFactoryInterface $encoderFactory)
    {
        $this->em = $em;
        $this->encoderFactory = $encoderFactory;
        $this->allowedRoles = ['ROLE_ADMIN', 'ROLE_EMPLOYEE'];
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('techmahindra:user:create')
            ->setDescription('Create a Tech Mahindra user');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $usernameQuestion = new Question('<question>Enter username to create: </question>', null);
        $usernameQuestion->setValidator(function ($answer) {

            if (!isset($answer)) {
                throw new \Exception('username is a mandatory field');
            }

            if ($this->em->getRepository(User::class)->findOneBy(['username' => $answer])) {
                throw new \RuntimeException($answer.' already exists');
            }

            return $answer;

        });
        $usernameQuestion->setMaxAttempts(3);

        $firstNameQuestion = new Question('<question>Enter user first name: </question>', null);
        $firstNameQuestion->setValidator(function ($answer) {
            if (!isset($answer)) {
                throw new \Exception('first name is a mandatory field');
            } else {
                return $answer;
            }
        });
        $firstNameQuestion->setMaxAttempts(3);

        $lastNameQuestion = new Question('<question>Enter user last name: </question>', null);
        $lastNameQuestion->setValidator(function ($answer) {
            if (!isset($answer)) {
                throw new \Exception('last name is a mandatory field');
            } else {
                return $answer;
            }
        });
        $lastNameQuestion->setMaxAttempts(3);

        $emailQuestion = new Question('<question>Enter user email: </question>', null);
        $emailQuestion->setValidator(function ($answer) {

            if (!isset($answer)) {
                throw new \Exception('Email is a mandatory field');
            }

            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Email is not valid');
            }

            return $answer;
        });
        $emailQuestion->setMaxAttempts(3);

        $rolesQuestion = new Question('<question>Enter user role: </question>', null);

        $rolesQuestion->setValidator(function ($answer) {
            if (!isset($answer)) {
                throw new \Exception('Role is a mandatory field');
            } else if(!in_array($answer, array_values($this->allowedRoles))) {
                throw new \Exception('Invalid Role please user ROLE_ADMIN or ROLE_EMPLOYEE');
            } else {
                return $answer;
            }
        });
        $rolesQuestion->setAutocompleterValues($this->roles);
        $rolesQuestion->setMaxAttempts(3);

        $this->username = $helper->ask($input, $output, $usernameQuestion);
        $this->firstName = $helper->ask($input, $output, $firstNameQuestion);
        $this->lastName = $helper->ask($input, $output, $lastNameQuestion);
        $this->email = $helper->ask($input, $output, $emailQuestion);
        $this->roles[] = $helper->ask($input, $output, $rolesQuestion);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();
        $user->setUsername($this->username);
        $user->setFirstName($this->firstName);
        $user->setLastName($this->lastName);
        $user->setEmail($this->email);
        $user->setRoles($this->roles);
        $user->setIsEnabled(true);

        $password = $user->generatePassword();
        $encoder = $this->encoderFactory->getEncoder($user);
        $user->encodePassword($encoder);
        $user->setRawPassword($password);


        $this->em->persist($user);
        $this->em->flush();

        $output->writeln($this->username.'|'.$password);
    }
}