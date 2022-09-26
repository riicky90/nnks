<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $Name;

    #[ORM\Column(type: 'string', length: 255)]
    private $MailTrainer;

    #[ORM\Column(type: 'text', nullable: true)]
    private $Comments;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: Dancers::class)]
    private $Trainer;

    #[ORM\ManyToOne(targetEntity: DanceCategory::class, inversedBy: 'teams')]
    private $Category;

    #[ORM\OneToMany(mappedBy: 'Team', targetEntity: Registrations::class,  fetch: 'EAGER')]
    private $registrations;


    #[ORM\Column(type: 'string', length: 255)]
    private $TrainerName;

    #[ORM\ManyToOne(inversedBy: 'teams')]
    #[Gedmo\Blameable(on: 'create')]
    private ?User $User = null;

    public function __construct()
    {
        $this->Trainer = new ArrayCollection();
        $this->registrations = new ArrayCollection();
    }

    public function __toString() {
        return $this->Name;
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

    public function getMailTrainer(): ?string
    {
        return $this->MailTrainer;
    }

    public function setMailTrainer(string $MailTrainer): self
    {
        $this->MailTrainer = $MailTrainer;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->Comments;
    }

    public function setComments(?string $Comments): self
    {
        $this->Comments = $Comments;

        return $this;
    }

    /**
     * @return Collection|Dancers[]
     */
    public function getTrainer(): Collection
    {
        return $this->Trainer;
    }

    public function addTrainer(Dancers $trainer): self
    {
        if (!$this->Trainer->contains($trainer)) {
            $this->Trainer[] = $trainer;
            $trainer->setTeam($this);
        }

        return $this;
    }

    public function removeTrainer(Dancers $trainer): self
    {
        if ($this->Trainer->removeElement($trainer)) {
            // set the owning side to null (unless already changed)
            if ($trainer->getTeam() === $this) {
                $trainer->setTeam(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?DanceCategory
    {
        return $this->Category;
    }

    public function setCategory(?DanceCategory $Category): self
    {
        $this->Category = $Category;

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
            $registration->setTeam($this);
        }

        return $this;
    }

    public function removeRegistration(Registrations $registration): self
    {
        if ($this->registrations->removeElement($registration)) {
            // set the owning side to null (unless already changed)
            if ($registration->getTeam() === $this) {
                $registration->setTeam(null);
            }
        }

        return $this;
    }

    public function getTrainerName(): ?string
    {
        return $this->TrainerName;
    }

    public function setTrainerName(string $TrainerName): self
    {
        $this->TrainerName = $TrainerName;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }
}
