<?php

namespace App\Entity;

use App\Repository\PromotionCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class PromotionCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="promotionCategories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $percent;

    /**
     * @ORM\OneToMany(targetEntity=Promotion::class, mappedBy="category")
     */
    private $promotions;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $howManyProducts;

    /**
     * @ORM\ManyToOne(targetEntity=PromotionCategoryStatus::class, inversedBy="promotionCategories")
     */
    private $status;

    public function __construct()
    {
        $this->promotions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPercent(): ?string
    {
        return $this->percent;
    }

    public function setPercent(string $percent): self
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * @return Collection<int, Promotion>
     */
    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    public function addPromotion(Promotion $promotion): self
    {
        if (!$this->promotions->contains($promotion)) {
            $this->promotions[] = $promotion;
            $promotion->setCategory($this);
        }

        return $this;
    }

    public function removePromotion(Promotion $promotion): self
    {
        if ($this->promotions->removeElement($promotion)) {
            // set the owning side to null (unless already changed)
            if ($promotion->getCategory() === $this) {
                $promotion->setCategory(null);
            }
        }

        return $this;
    }

    public function getHowManyProducts(): ?string
    {
        return $this->howManyProducts;
    }

    public function setHowManyProducts(?string $howManyProducts): self
    {
        $this->howManyProducts = $howManyProducts;

        return $this;
    }

    public function getStatus(): ?PromotionCategoryStatus
    {
        return $this->status;
    }

    public function setStatus(?PromotionCategoryStatus $status): self
    {
        $this->status = $status;

        return $this;
    }
}
