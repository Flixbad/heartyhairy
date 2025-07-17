<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    private ?Dish $dish = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    private ?Order $orderObject = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
private ?User $user = null;

#[ORM\Column(type: 'datetime')]
private ?\DateTimeInterface $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getDish(): ?Dish
    {
        return $this->dish;
    }

    public function setDish(?Dish $dish): static
    {
        $this->dish = $dish;

        return $this;
    }

    public function getOrderObject(): ?Order
    {
        return $this->orderObject;
    }

    public function setOrderObject(?Order $orderObject): static
    {
        $this->orderObject = $orderObject;

        return $this;
    }
}
