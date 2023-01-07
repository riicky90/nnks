<?php

namespace App\Entity;

use App\Repository\RegistrationsRepository;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Craue\ConfigBundle\Util\Config;

#[ORM\Entity(repositoryClass: RegistrationsRepository::class)]
#[Gedmo\Loggable]
#[ORM\HasLifecycleCallbacks]
class Registrations
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Gedmo\Versioned]
    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'registrations')]
    private $Team;

    #[ORM\ManyToMany(targetEntity: Dancers::class, inversedBy: 'registrations', cascade: ['all'], fetch: 'EAGER')]
    private $Dancers;

    #[Gedmo\Versioned]
    #[ORM\Column(type: 'text', nullable: true)]
    private $Comments;

    #[Gedmo\Versioned]
    #[ORM\ManyToOne(targetEntity: Contest::class, fetch: 'EAGER', inversedBy: 'registrations')]
    #[ORM\JoinColumn(nullable: false)]
    private $Contest;

    #[ORM\OneToMany(mappedBy: 'Registration', targetEntity: Orders::class, cascade: ['detach'], orphanRemoval: true)]
    private $orders;

    #[Gedmo\Versioned]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $MusicFile;

    #[ORM\ManyToOne(inversedBy: 'registrations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Gedmo\Blameable(on: 'create')]
    private ?User $CreatedBy = null;


    public function __construct()
    {
        $this->Dancers = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam(): ?Team
    {
        return $this->Team;
    }

    public function setTeam(?Team $Team): self
    {
        $this->Team = $Team;

        return $this;
    }

    /**
     * @return Collection|Dancers[]
     */
    public function getDancers(): Collection
    {
        return $this->Dancers;
    }

    public function addDancer(Dancers $dancer): self
    {
        if (!$this->Dancers->contains($dancer)) {
            $this->Dancers[] = $dancer;
        }

        return $this;
    }

    public function removeDancer(Dancers $dancer): self
    {
        $this->Dancers->removeElement($dancer);

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

    public function getContest(): ?Contest
    {
        return $this->Contest;
    }

    public function setContest(?Contest $Contest): self
    {
        $this->Contest = $Contest;

        return $this;
    }

    /**
     * @return Collection<int, Orders>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Orders $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setRegistration($this);
        }

        return $this;
    }

    public function removeOrder(Orders $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getRegistration() === $this) {
                $order->setRegistration(null);
            }
        }

        return $this;
    }

    public function getMusicFile(): ?string
    {
        return $this->MusicFile;
    }

    public function setMusicFile(string $MusicFile): self
    {
        $this->MusicFile = $MusicFile;

        return $this;
    }

    public function getNumberOfDancers(): int
    {
        return count($this->Team->getDancers());
    }

    public function getTotalpaid()
    {
        $orders = $this->getOrders();

        $totalpaid = 0;

        foreach ($orders as $order) {
            if ($order->getOrderStatus() == 'paid') {
                $totalpaid += $order->getAmount();
            }
        }

        return $totalpaid;
    }

    public function getCreatedBy(): ?User
    {
        return $this->CreatedBy;
    }

    public function setCreatedBy(?User $CreatedBy): self
    {
        $this->CreatedBy = $CreatedBy;

        return $this;
    }
}