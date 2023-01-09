<?php

namespace App\Entity\Discount;

use App\Entity\Order;
use App\Entity\Timestamp;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Discount
{
    use Timestamp;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=DiscountOrder::class, inversedBy="discounts")
     */
    private $discountOrder;

    /**
     * @ORM\ManyToOne(targetEntity=DiscountCategory::class, inversedBy="discounts")
     */
    private $discountCategory;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="discounts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $orders;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDOrder(): ?DiscountOrder
    {
        return $this->discountOrder;
    }

    public function setDOrder(?DiscountOrder $discountOrder): self
    {
        $this->discountOrder = $discountOrder;

        return $this;
    }

    public function getDCategory(): ?DiscountCategory
    {
        return $this->discountCategory;
    }

    public function setDCategory(?DiscountCategory $discountCategory): self
    {
        $this->discountCategory = $discountCategory;

        return $this;
    }

    public function getOrders(): ?Order
    {
        return $this->orders;
    }

    public function setOrders(?Order $orders): self
    {
        $this->orders = $orders;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
