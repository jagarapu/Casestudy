<?php

namespace App\Entity;

use App\Repository\OfficeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=OfficeRepository::class)
 */
class Office
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $postCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phoneNumber;

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
}
