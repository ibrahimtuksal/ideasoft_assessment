<?php

namespace App\Entity;

use App\Repository\PromotionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Promotion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=PromotionBasket::class, inversedBy="promotions")
     */
    private $basket;

    /**
     * @ORM\ManyToOne(targetEntity=PromotionCategory::class, inversedBy="promotions")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="promotions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $orderId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBasket(): ?PromotionBasket
    {
        return $this->basket;
    }

    public function setBasket(?PromotionBasket $basket): self
    {
        $this->basket = $basket;

        return $this;
    }

    public function getCategory(): ?PromotionCategory
    {
        return $this->category;
    }

    public function setCategory(?PromotionCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getOrderId(): ?Order
    {
        return $this->orderId;
    }

    public function setOrderId(?Order $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }
}
