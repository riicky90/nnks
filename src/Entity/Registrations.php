<?php

namespace App\Entity;

use App\Repository\RegistrationsRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: RegistrationsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Registrations
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'registrations')]
    private $Team;

    #[ORM\ManyToMany(targetEntity: Dancers::class, inversedBy: 'registrations', fetch: 'EAGER')]
    private $Dancers;

    #[ORM\Column(type: 'text', nullable: true)]
    private $Comments;

    #[ORM\ManyToOne(targetEntity: Contest::class, fetch: 'EAGER', inversedBy: 'registrations')]
    #[ORM\JoinColumn(nullable: false)]
    private $Contest;

    #[ORM\Column(type: 'string', length: 255)]
    #[Gedmo\Blameable(on: 'create')]
    private $RegisteredBy;

    #[ORM\OneToMany(mappedBy: 'Registration', targetEntity: Orders::class)]
    private $orders;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $MusicFile;

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

    public function getRegisteredBy(): ?string
    {
        return $this->RegisteredBy;
    }

    public function setRegisteredBy(?string $RegisteredBy): self
    {
        $this->RegisteredBy = $RegisteredBy;

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

    public function getRegistrationStatus()
    {
        $orders = $this->getOrders();

        $totalPayed = 0;

        foreach ($orders as $order) {
            if ($order->getOrderStatus() == 'payed') {
                $totalPayed += $order->getAmount();
            }
        }

        $dancers = $this->getDancers();
        $music = $this->getMusicFile();

        $totalDue = count($dancers) * 5.00;

        if (count($dancers) >= 4 && $music && $totalPayed >= $totalDue) {
            return [
                'status' => true
            ];
        } else {

            $reason = "";
            //check if requirements are met
            if (count($dancers) <= 4) {
                $reason .= "Aantal dansers is minder dan 5 &#013;";
            }if(count($dancers) == 0) {
                $reason .= "Er zijn nog geen dansers ingegeven &#013;";
            }if (!$music) {
                $reason .= "Er is nog geen muziek geupload &#013;";
            }
            if ($totalPayed < $totalDue) {
                $reason .= "Het volledige bedrag is nog niet betaald &#013;";
            }

            return [
                'status' => false,
                'reason' => $reason
            ];
        }
    }
}