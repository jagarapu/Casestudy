<?php

namespace App\Entity;

use App\Entity\SpadeLite\Organization;
use App\Repository\OfficeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="office")
 * @ORM\Entity(repositoryClass=OfficeRepository::class)
 */
class Office
{
    use TimestampableEntity;

    public static $logoPath = 'uploads/logos';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank(message="Title cannot be blank", groups={"office"})
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * @Assert\NotBlank(message="Addressname cannot be blank",
     * groups={"office"})
     * @Assert\Length(min=4,max=400,minMessage="Addressname should be minimum {{ limit }} characters", maxMessage="Your Addressname cannot be longer than {{ limit }} characters",groups={"office"}
     * )
     * @Assert\Regex(
     *      pattern="/[^a-zA-Zà-ÿÀ-Ÿ0-9- .\,\-\'\#\/]/i",
     *      match = false,
     *      message = "Addressname allows only a-z,A-Z,à-ÿ,À-Ÿ,0-9 and few special characters(. - ' # / ,)", groups={"office"})
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @var string
     * @Assert\NotBlank(message="Postal code cannot be blank",groups={"office"})
     * @Assert\Length(min=5,max=7,minMessage="Postal Code should be minimum {{ limit }} characters", maxMessage="Your Postal Code cannot be longer than {{ limit }} characters",groups={"office"}
     * )
     * @Assert\Regex(
     *      pattern="/[^a-zA-Zà-ÿÀ-Ÿ0-9- .\-\'\#\/]/i",
     *      match = false,
     *      message = "Postal code allows only a-z,A-Z,à-ÿ,À-Ÿ,0-9 and few special characters(. - ' # /)", groups={"office"})
     * @ORM\Column(type="string", length=255)
     */
    private $postCode;

    /**
     * @var string
     * @Assert\NotBlank(message="PhoneNumber cannot be blank",groups={"office"})
     * @Assert\Length(min=8,max=18,minMessage="Phone Number should be minimum {{ limit }} characters", maxMessage="Your Landline cannot be longer than {{ limit }} characters",groups={"office"}
     * )
     * @Assert\Regex(
     *      pattern="/[^0-9 +\-]/",
     *      match = false,
     *      message = "Landline allows only (0-9,+,-)", groups={"office"})
     * @ORM\Column(type="string", length=18)
     */
    private $phoneNumber;

    /**
     * @var string
     * @Assert\NotBlank(message="City cannot be blank",groups={"office"})
     * @Assert\Length(min=2,max=60,minMessage="City should be minimum {{ limit }} characters", maxMessage="Your City cannot be longer than {{ limit }} characters",groups={"office"}
     * )
     * @Assert\Regex(
     *      pattern="/[^a-zA-Zà-ÿÀ-Ÿ '\-]/i",
     *      match = false,
     *      message = "City allows only a-z,A-Z,à-ÿ,À-Ÿ and one special character(')", groups={"office"})
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity=OfficeOccupancy::class, mappedBy="office")
     */
    private $officeOccupancies;

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
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isEnabled = false;

    /**
     * @var string
     * @Assert\NotBlank(message="State cannot be blank",groups={"office"})
     * @Assert\Length(min=2,max=60,minMessage="State should be minimum {{ limit }} characters", maxMessage="Your State cannot be longer than {{ limit }} characters",groups={"office"}
     * )
     * @Assert\Regex(
     *      pattern="/[^a-zA-Zà-ÿÀ-Ÿ ']/i",
     *      match = false,
     *      message = "State allows only a-z,A-Z,à-ÿ,À-Ÿ and one special character(')", groups={"office"})
     * @ORM\Column(type="string", length=255)
     */
    protected $state;

    /**
     * @var string
     * @Assert\NotBlank(message="Country cannot be blank",groups={"office"})
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @Assert\NotBlank(message="Office Capacity cannot be blank",groups={"office"})
     * @ORM\Column(type="integer")
     */
    private $officeCapacity;

    public function __construct()
    {
        $this->officeOccupancies = new ArrayCollection();
        $this->isEnabled = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(string $postCode): self
    {
        $this->postCode = $postCode;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

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
            $officeOccupancy->setOffice($this);
        }

        return $this;
    }

    public function removeOfficeOccupancy(OfficeOccupancy $officeOccupancy): self
    {
        if ($this->officeOccupancies->removeElement($officeOccupancy)) {
            // set the owning side to null (unless already changed)
            if ($officeOccupancy->getOffice() === $this) {
                $officeOccupancy->setOffice(null);
            }
        }

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

    public function getIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(?bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo($logo): self
    {
        if ($logo) {

            $fileName = $this->generateUniqueFileName().'.'.$logo->guessExtension();

            try {
                $logo->move(
                    getcwd().'/'.Office::$logoPath,
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            $this->logo = $fileName;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

    public function removeLogo()
    {
        $this->removeUpload();
        $this->logo = null;
        return $this;
    }

    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            if (file_exists($file)) {
                unlink($file);
            }
            return true;
        }

        return false;
    }

    public function getAbsolutePath()
    {
        if (null === $this->logo) {
            //
        } else {
            return getcwd().'/'.Office::$logoPath . '/' . $this->logo;
        }
    }

    public function getOfficeCapacity(): ?int
    {
        return $this->officeCapacity;
    }

    public function setOfficeCapacity(int $officeCapacity): self
    {
        $this->officeCapacity = $officeCapacity;

        return $this;
    }

}
