<?php

namespace App\Entity;

use App\Repository\ContestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: 'json')]
    private $Disciplines = [];

    #[ORM\OneToMany(mappedBy: 'Contest', targetEntity: EventScan::class, orphanRemoval: true)]
    private Collection $eventScans;

    #[ORM\Column(length: 255)]
    private ?string $LocationAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $LocationZipCode = null;

    #[ORM\Column(length: 255)]
    private ?string $LocationCity = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $RegistrationOpenFrom = null;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
        $this->eventScans = new ArrayCollection();
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

    public function getDisciplines(): array
    {
        return $this->Disciplines;
    }

    public function setDisciplines(array $Disciplines): self
    {
        $this->Disciplines = $Disciplines;

        return $this;
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
            $eventScan->setContest($this);
        }

        return $this;
    }

    public function removeEventScan(EventScan $eventScan): self
    {
        if ($this->eventScans->removeElement($eventScan)) {
            // set the owning side to null (unless already changed)
            if ($eventScan->getContest() === $this) {
                $eventScan->setContest(null);
            }
        }

        return $this;
    }

    const DICIPLINESLIST = array(
        'Streetdance/HipHop' => 'Streetdance/HipHop',
        'Showdance' => 'Showdance',
        'Moderne dans' => 'Moderne dans',
        'Mega Crew Dance' => 'Mega Crew Dance'
    );

    public function getLocationAddress(): ?string
    {
        return $this->LocationAddress;
    }

    public function setLocationAddress(string $LocationAddress): self
    {
        $this->LocationAddress = $LocationAddress;

        return $this;
    }

    public function getLocationZipCode(): ?string
    {
        return $this->LocationZipCode;
    }

    public function setLocationZipCode(string $LocationZipCode): self
    {
        $this->LocationZipCode = $LocationZipCode;

        return $this;
    }

    public function getLocationCity(): ?string
    {
        return $this->LocationCity;
    }

    public function setLocationCity(string $LocationCity): self
    {
        $this->LocationCity = $LocationCity;

        return $this;
    }

    public function getRegistrationOpenFrom(): ?\DateTimeInterface
    {
        return $this->RegistrationOpenFrom;
    }

    public function setRegistrationOpenFrom(\DateTimeInterface $RegistrationOpenFrom): self
    {
        $this->RegistrationOpenFrom = $RegistrationOpenFrom;

        return $this;
    }
}
