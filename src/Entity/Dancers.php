<?php

namespace App\Entity;

use App\Repository\DancersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DancersRepository::class)]
class Dancers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $FirstName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $SecondName;

    #[ORM\Column(type: 'string', length: 255)]
    private $LastName;

    #[ORM\Column(type: 'date')]
    private $BirthDay;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'Trainer')]
    private $team;

    #[ORM\ManyToMany(targetEntity: Registrations::class, mappedBy: 'Dancers')]
    private $registrations;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
    }
    public function __toString() {
        return $this->FirstName. ' '. $this->SecondName. ' '. $this->LastName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->FirstName;
    }

    public function setFirstName(string $FirstName): self
    {
        $this->FirstName = $FirstName;

        return $this;
    }

    public function getSecondName(): ?string
    {
        return $this->SecondName;
    }

    public function setSecondName(?string $SecondName): self
    {
        $this->SecondName = $SecondName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->LastName;
    }

    public function setLastName(string $LastName): self
    {
        $this->LastName = $LastName;

        return $this;
    }

    public function getBirthDay(): ?\DateTimeInterface
    {
        return $this->BirthDay;
    }

    public function setBirthDay(\DateTimeInterface $BirthDay): self
    {
        $this->BirthDay = $BirthDay;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    /**
     * @return Collection|Registrations[]
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function addRegistration(Registrations $registration): self
    {
        if (!$this->registrations->contains($registration)) {
            $this->registrations[] = $registration;
            $registration->addDancer($this);
        }

        return $this;
    }

    public function removeRegistration(Registrations $registration): self
    {
        if ($this->registrations->removeElement($registration)) {
            $registration->removeDancer($this);
        }

        return $this;
    }
}
