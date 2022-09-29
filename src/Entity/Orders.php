<?php

namespace App\Entity;

use App\Repository\OrdersRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: OrdersRepository::class)]
class Orders
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $OrderNumber;

    #[ORM\Column(type: 'string', length: 255)]
    private $OrderStatus;

    #[ORM\ManyToOne(targetEntity: Registrations::class, inversedBy: 'orders')]
    private $Registration;

    #[ORM\Column(type: 'string', length: 255)]
    #[Gedmo\Blameable(on: 'create')]
    private $CompletedBy;


    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private $Amount;

    #[ORM\Column(length: 255)]
    private ?string $OrderId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): ?string
    {
        return $this->OrderNumber;
    }

    public function setOrderNumber(string $OrderNumber): self
    {
        $this->OrderNumber = $OrderNumber;

        return $this;
    }

    public function getOrderStatus(): ?string
    {
        return $this->OrderStatus;
    }

    public function setOrderStatus(string $OrderStatus): self
    {
        $this->OrderStatus = $OrderStatus;

        return $this;
    }

    public function getRegistration(): ?Registrations
    {
        return $this->Registration;
    }

    public function setRegistration(?Registrations $Registration): self
    {
        $this->Registration = $Registration;

        return $this;
    }

    public function getCompletedBy(): ?string
    {
        return $this->CompletedBy;
    }

    public function setCompletedBy(string $CompletedBy): self
    {
        $this->CompletedBy = $CompletedBy;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->Amount;
    }

    public function setAmount(?string $Amount): self
    {
        $this->Amount = $Amount;

        return $this;
    }

    public function getOrderId(): ?string
    {
        return $this->OrderId;
    }

    public function setOrderId(string $OrderId): self
    {
        $this->OrderId = $OrderId;

        return $this;
    }
}
