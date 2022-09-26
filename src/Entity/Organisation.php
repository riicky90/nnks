<?php

namespace App\Entity;

use App\Repository\OrganisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganisationRepository::class)]
class Organisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $Name;

    #[ORM\Column(type: 'string', length: 255)]
    private $Adres;

    #[ORM\Column(type: 'string', length: 255)]
    private $ZipCode;

    #[ORM\Column(type: 'string', length: 255)]
    private $City;

    #[ORM\Column(type: 'string', length: 255)]
    private $Email;

    #[ORM\Column(type: 'string', length: 255)]
    private $Phone;

    #[ORM\OneToMany(mappedBy: 'Organisation', targetEntity: Contest::class)]
    private $contests;

    #[ORM\OneToMany(mappedBy: 'Organisation', targetEntity: User::class)]
    private $users;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $MollieApiKey;

    #[ORM\Column(type: 'text', nullable: true)]
    private $Description;

    public function __construct()
    {
        $this->contests = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function __toString()
    {
        return strval( $this->Name);
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

    public function getAdres(): ?string
    {
        return $this->Adres;
    }

    public function setAdres(string $Adres): self
    {
        $this->Adres = $Adres;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->ZipCode;
    }

    public function setZipCode(string $ZipCode): self
    {
        $this->ZipCode = $ZipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->City;
    }

    public function setCity(string $City): self
    {
        $this->City = $City;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): self
    {
        $this->Email = $Email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->Phone;
    }

    public function setPhone(string $Phone): self
    {
        $this->Phone = $Phone;

        return $this;
    }

    /**
     * @return Collection|Contest[]
     */
    public function getContests(): Collection
    {
        return $this->contests;
    }

    public function addContest(Contest $contest): self
    {
        if (!$this->contests->contains($contest)) {
            $this->contests[] = $contest;
            $contest->setOrganisation($this);
        }

        return $this;
    }

    public function removeContest(Contest $contest): self
    {
        if ($this->contests->removeElement($contest)) {
            // set the owning side to null (unless already changed)
            if ($contest->getOrganisation() === $this) {
                $contest->setOrganisation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setOrganisation($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getOrganisation() === $this) {
                $user->setOrganisation(null);
            }
        }

        return $this;
    }

    public function getMollieApiKey(): ?string
    {
        return $this->MollieApiKey;
    }

    public function setMollieApiKey(?string $MollieApiKey): self
    {
        $this->MollieApiKey = $MollieApiKey;

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
}
