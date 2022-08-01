<?php

namespace App\Entity;

use App\Repository\ContestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContestRepository::class)]
class Contest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $Name;

    #[ORM\Column(type: 'date')]
    private $Date;

    #[ORM\Column(type: 'string', length: 255)]
    private $Location;

    #[ORM\ManyToOne(targetEntity: Organisation::class, inversedBy: 'contests')]
    #[ORM\JoinColumn(nullable: false)]
    private $Organisation;

    #[ORM\Column(type: 'text')]
    private $Description;

    #[ORM\OneToMany(mappedBy: 'Contest', targetEntity: Registrations::class)]
    private $registrations;

    #[ORM\Column(type: 'boolean')]
    private $Enabled;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private $RegistrationFee;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private $EntranceFee;

    #[ORM\Column(type: 'text')]
    private $Disciplines;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): self
    {
        $this->Date = $Date;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->Location;
    }

    public function setLocation(string $Location): self
    {
        $this->Location = $Location;

        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->Organisation;
    }

    public function setOrganisation(?Organisation $Organisation): self
    {
        $this->Organisation = $Organisation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): self
    {
        $this->Description = $Description;

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
            $registration->setContest($this);
        }

        return $this;
    }

    public function removeRegistration(Registrations $registration): self
    {
        if ($this->registrations->removeElement($registration)) {
            // set the owning side to null (unless already changed)
            if ($registration->getContest() === $this) {
                $registration->setContest(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return strval( $this->Name. ' - '. $this->Date->format('d-m-Y'));
    }

    public function getEnabled(): ?bool
    {
        return $this->Enabled;
    }

    public function setEnabled(bool $Enabled): self
    {
        $this->Enabled = $Enabled;

        return $this;
    }

    public function getRegistrationFee(): ?string
    {
        return $this->RegistrationFee;
    }

    public function setRegistrationFee(string $RegistrationFee): self
    {
        $this->RegistrationFee = $RegistrationFee;

        return $this;
    }

    public function getEntranceFee(): ?string
    {
        return $this->EntranceFee;
    }

    public function setEntranceFee(string $EntranceFee): self
    {
        $this->EntranceFee = $EntranceFee;

        return $this;
    }

    public function getDisciplines(): ?string
    {
        return $this->Disciplines;
    }

    public function setDisciplines(string $Disciplines): self
    {
        $this->Disciplines = $Disciplines;

        return $this;
    }
}
