<?php

namespace App\Entity;

use App\Repository\DancersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DancersRepository::class)]
class Dancers extends \App\Repository\TeamRepository
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

    #[ORM\ManyToMany(targetEntity: Registrations::class, mappedBy: 'Dancers',cascade: ['detach'])]
    private $registrations;

    #[ORM\OneToMany(mappedBy: 'Dancer', targetEntity: EventScan::class, orphanRemoval: true)]
    private Collection $eventScans;

    #[ORM\ManyToMany(targetEntity: Team::class, inversedBy: 'dancers')]
    private Collection $Teams;


    public function __construct()
    {
        $this->registrations = new ArrayCollection();
        $this->eventScans = new ArrayCollection();
        $this->Teams = new ArrayCollection();
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

    public function getFullName()
    {
        return $this->FirstName. ' '. $this->SecondName. ' '. $this->LastName;
    }

    public function getAllDetails()
    {
        return $this->FirstName. ' '. $this->SecondName. ' '. $this->LastName. ' | '. $this->BirthDay->format('d-m-Y');
    }

    /**
     * @return Collection<int, EventScan>
     */
    public function getEventScans(): Collection
    {
        return $this->eventScans;
    }

    public function addEventScan(EventScan $eventScan): self
    {
        if (!$this->eventScans->contains($eventScan)) {
            $this->eventScans->add($eventScan);
            $eventScan->setDancer($this);
        }

        return $this;
    }

    public function removeEventScan(EventScan $eventScan): self
    {
        if ($this->eventScans->removeElement($eventScan)) {
            // set the owning side to null (unless already changed)
            if ($eventScan->getDancer() === $this) {
                $eventScan->setDancer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->Teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->Teams->contains($team)) {
            $this->Teams->add($team);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        $this->Teams->removeElement($team);

        return $this;
    }

}
