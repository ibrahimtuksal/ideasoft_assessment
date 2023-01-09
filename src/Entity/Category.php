<?php

namespace App\Entity;

use App\Entity\Discount\Discount;
use App\Entity\Discount\DiscountCategory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Category
{
    use Timestamp;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Discount::class, mappedBy="applicableCategory")
     */
    private $discounts;

    /**
     * @ORM\OneToMany(targetEntity=DiscountCategory::class, mappedBy="category")
     */
    private $discountCategories;

    /**
     * @ORM\OneToMany(targetEntity=OrderItem::class, mappedBy="category")
     */
    private $orderItems;

    public function __construct()
    {
        $this->discounts = new ArrayCollection();
        $this->discountCategories = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Discount>
     */
    public function getDiscounts(): Collection
    {
        return $this->discounts;
    }

    public function addDiscount(Discount $discount): self
    {
        if (!$this->discounts->contains($discount)) {
            $this->discounts[] = $discount;
            $discount->setApplicableCategory($this);
        }

        return $this;
    }

    public function removeDiscount(Discount $discount): self
    {
        if ($this->discounts->removeElement($discount)) {
            // set the owning side to null (unless already changed)
            if ($discount->getApplicableCategory() === $this) {
                $discount->setApplicableCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DiscountCategory>
     */
    public function getDiscountCategories(): Collection
    {
        return $this->discountCategories;
    }

    public function addDiscountCategory(DiscountCategory $discountCategory): self
    {
        if (!$this->discountCategories->contains($discountCategory)) {
            $this->discountCategories[] = $discountCategory;
            $discountCategory->setCategory($this);
        }

        return $this;
    }

    public function removeDiscountCategory(DiscountCategory $discountCategory): self
    {
        if ($this->discountCategories->removeElement($discountCategory)) {
            // set the owning side to null (unless already changed)
            if ($discountCategory->getCategory() === $this) {
                $discountCategory->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
            $orderItem->setCategory($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getCategory() === $this) {
                $orderItem->setCategory(null);
            }
        }

        return $this;
    }
}
