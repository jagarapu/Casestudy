<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields="username",message="Username already exists in database",groups={"userregistration"})
 * @UniqueEntity(fields="email",message = "Email already exists in database",groups={"userregistration"})
 *
 */
class User implements UserInterface
{
    use TimestampableEntity;

    const PASSWORD_LENGTH = 8;

    const ROLE_DEFAULT = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_SUPER_ADMIN';
    const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    const SALUTATION_MR = 'Mister';
    const SALUTATION_MS = 'Miss';
    const SALUTATION_MRS = 'Mrs.';
    const SALUTATION_DR = 'Doctor';

    const GENDER_MALE = 'm';
    const GENDER_FEMALE = 'f';
    const GENDER_NON_BINARY = 'n';
    const GENDER_UNKNOWN = 'u';

    public static $getSalutation = [
        'Mister' => self::SALUTATION_MR,
        'Miss' => self::SALUTATION_MS,
        'Mrs.' => self::SALUTATION_MRS,
        'Doctor' => self::SALUTATION_DR
    ];

    public static $getGender = [
        'Male' => self::GENDER_MALE,
        'Female' => self::GENDER_FEMALE,
        'Non-Binary' => self::GENDER_NON_BINARY,
        'Unknown' => self::GENDER_UNKNOWN,
    ];

    public static $getRoles = [
        'Admin' => self::ROLE_ADMIN,
        'Employee' => self::ROLE_EMPLOYEE,
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank( message = "Username cannot be blank", groups={"userregistration"})
     * @Assert\Regex(
     *      pattern="/[^a-zA-Z0-9-_]/",
     *      match = false,
     *      message = "Username allows only a-z,A-Z,0-9,_", groups={"userregistration"})
     * @Assert\Length(min=4,max=30,minMessage="Username should be minimum {{ limit }} characters",maxMessage="Username cannot exceeds {{ limit }} characters",groups={"userregistration"})
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @Assert\NotBlank(message="Firstname cannot be blank", groups={"userregistration"})
     * @Assert\Length(min=2,max=100,minMessage="Firstname should be minimum {{ limit }} characters", maxMessage="Your Firstname cannot be longer than {{ limit }} characters",groups={"userregistration"}
     * )
     * @Assert\Regex(
     *      pattern="/[^A-Za-zà-ÿÀ-Ÿ .\']/i",
     *      match=false,
     *      message="Firstname allows only a-z,A-Z and few special characters (. ')",groups={"userregistration"})
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @Assert\Email(message = "Email not valid", groups={"userregistration"})
     * @Assert\NotBlank(message = "Email cannot be blank", groups={"userregistration"})
     * @Assert\Length(max=255,maxMessage="Email cannot exceeds 255 characters")
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isEnabled;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $createdBy;

    /**
     * @Gedmo\Blameable(on="update")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLoginAt;

    /**
     * @ORM\OneToMany(targetEntity=OfficeOccupancy::class, mappedBy="user")
     */
    private $officeOccupancies;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $salt;

    private $rawPassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $employeeId;

    public function __construct()
    {
        $this->officeOccupancies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
         $this->rawPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(?bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getLastLoginAt(): ?\DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?\DateTimeInterface $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    /**
     * @return Collection|OfficeOccupancy[]
     */
    public function getOfficeOccupancies(): Collection
    {
        return $this->officeOccupancies;
    }

    public function addOfficeOccupancy(OfficeOccupancy $officeOccupancy): self
    {
        if (!$this->officeOccupancies->contains($officeOccupancy)) {
            $this->officeOccupancies[] = $officeOccupancy;
            $officeOccupancy->setUser($this);
        }

        return $this;
    }

    public function removeOfficeOccupancy(OfficeOccupancy $officeOccupancy): self
    {
        if ($this->officeOccupancies->removeElement($officeOccupancy)) {
            // set the owning side to null (unless already changed)
            if ($officeOccupancy->getUser() === $this) {
                $officeOccupancy->setUser(null);
            }
        }

        return $this;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function encodePassword(PasswordEncoderInterface $encoder)
    {
        if ($this->rawPassword) {
            $this->salt = sha1(uniqid(mt_rand()));
            $this->password = $encoder->encodePassword($this->rawPassword, $this->salt);

            $this->eraseCredentials();
        }
    }

    public function generatePassword()
    {
        $password = substr(md5(uniqid(mt_rand(), true)), 0, self::PASSWORD_LENGTH);
        $this->rawPassword = $password;

        return $password;
    }

    public function setRawPassword($rawPassword)
    {
        $this->rawPassword = $rawPassword;
    }

    public function getRawPassword()
    {
        return $this->rawPassword;
    }

    public function getEmployeeId(): ?string
    {
        return $this->employeeId;
    }

    public function setEmployeeId(?string $employeeId): self
    {
        $this->employeeId = $employeeId;

        return $this;
    }

}
