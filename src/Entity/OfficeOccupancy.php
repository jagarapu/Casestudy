<?php

namespace App\Entity;

use App\Repository\OfficeOccupancyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="office_occupancy")
 * @ORM\Entity(repositoryClass=OfficeOccupancyRepository::class)
 */
class OfficeOccupancy
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $entryTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $exitTime;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="officeOccupancies", cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Office::class, inversedBy="officeOccupancies", cascade={"persist"})
     */
    private $office;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntryTime(): ?\DateTimeInterface
    {
        return $this->entryTime;
    }

    public function setEntryTime(\DateTimeInterface $entryTime): self
    {
        $this->entryTime = $entryTime;

        return $this;
    }

    public function getExitTime(): ?\DateTimeInterface
    {
        return $this->exitTime;
    }

    public function setExitTime(\DateTimeInterface $exitTime = null): self
    {
        $this->exitTime = $exitTime;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getOffice(): ?Office
    {
        return $this->office;
    }

    public function setOffice(?Office $office): self
    {
        $this->office = $office;

        return $this;
    }
}
