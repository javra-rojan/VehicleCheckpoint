<?php

namespace App\Entity;

use App\Repository\ParkingDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParkingDetailRepository::class)]
class ParkingDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 30)]
    private $location;


    #[ORM\Column(type: 'string', length: 20)]
    private $PlateNo;

    #[ORM\Column(type: 'datetime')]
    private $EntryAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $ExitAt;

    #[ORM\Column(type: 'string', length: 10, unique: true)]
    private $ticket;

    #[ORM\ManyToOne(targetEntity: Driver::class, inversedBy: 'ParkingDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private $driver;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getPlateNo(): ?string
    {
        return $this->PlateNo;
    }

    public function setPlateNo(string $PlateNo): self
    {
        $this->PlateNo = $PlateNo;

        return $this;
    }

    public function getEntryAt(): ?\DateTimeInterface
    {
        return $this->EntryAt;
    }

    public function setEntryAt(\DateTimeInterface $EntryAt): self
    {
        $this->EntryAt = $EntryAt;

        return $this;
    }

    public function getExitAt(): ?\DateTimeInterface
    {
        return $this->ExitAt;
    }

    public function setExitAt(?\DateTimeInterface $ExitAt): self
    {
        $this->ExitAt = $ExitAt;

        return $this;
    }

    public function getTicket(): ?string
    {
        return $this->ticket;
    }

    public function setTicket(string $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    public function setDriver(?Driver $driver): self
    {
        $this->driver = $driver;
        return $this;
    }
}
