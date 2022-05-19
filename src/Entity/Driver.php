<?php

namespace App\Entity;

use App\Repository\DriverRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DriverRepository::class)]
#[UniqueEntity('email')]
class Driver
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 30)]
    #[Assert\NotBlank]
    private $name;

    #[ORM\Column(type: 'string', length: 15)]
    #[Assert\NotBlank]
    private $phone;

    #[ORM\Column(type: 'string', length: 30)]
    #[Assert\NotBlank]
    private $address;

    #[ORM\Column(type: 'integer', length:6 ,unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 6, max: 6)]
    private $license_no;

    #[ORM\OneToMany(mappedBy: 'driver', targetEntity: ParkingDetail::class)]
    private $ParkingDetails;

    public function __construct()
    {
        $this->ParkingDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

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

    public function getLicenseNo(): ?int
    {
        return $this->license_no;
    }

    public function setLicenseNo(int $license_no): self
    {
        $this->license_no = $license_no;

        return $this;
    }

    /**
     * @return Collection<int, ParkingDetail>
     */
    public function getParkingDetails(): Collection
    {
        return $this->ParkingDetails;
    }

    public function addParkingDetail(ParkingDetail $parkingDetail): self
    {
        if (!$this->ParkingDetails->contains($parkingDetail)) {
            $this->ParkingDetails[] = $parkingDetail;
            $parkingDetail->setDriver($this);
        }

        return $this;
    }

    public function removeParkingDetail(ParkingDetail $parkingDetail): self
    {
        if ($this->ParkingDetails->removeElement($parkingDetail)) {
            // set the owning side to null (unless already changed)
            if ($parkingDetail->getDriver() === $this) {
                $parkingDetail->setDriver(null);
            }
        }

        return $this;
    }


}
