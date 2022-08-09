<?php

namespace App\Entity;

use App\Repository\EventScanRepository;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: EventScanRepository::class)]
#[ORM\HasLifecycleCallbacks]
class EventScan
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'eventScans')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Contest $Contest = null;

    #[ORM\ManyToOne(inversedBy: 'eventScans')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dancers $Dancer = null;

    #[ORM\ManyToOne]
    #[Gedmo\Blameable(on: 'create')]
    private ?User $ScannedBy = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDancer(): ?Dancers
    {
        return $this->Dancer;
    }

    public function setDancer(?Dancers $Dancer): self
    {
        $this->Dancer = $Dancer;

        return $this;
    }

    public function getScannedBy(): ?User
    {
        return $this->ScannedBy;
    }

    public function setScannedBy(?User $ScannedBy): self
    {
        $this->ScannedBy = $ScannedBy;

        return $this;
    }
}
