<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Category
{
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
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="category")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity=PromotionCategory::class, mappedBy="category")
     */
    private $promotionCategories;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->promotionCategories = new ArrayCollection();
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
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PromotionCategory>
     */
    public function getPromotionCategories(): Collection
    {
        return $this->promotionCategories;
    }

    public function addPromotionCategory(PromotionCategory $promotionCategory): self
    {
        if (!$this->promotionCategories->contains($promotionCategory)) {
            $this->promotionCategories[] = $promotionCategory;
            $promotionCategory->setCategory($this);
        }

        return $this;
    }

    public function removePromotionCategory(PromotionCategory $promotionCategory): self
    {
        if ($this->promotionCategories->removeElement($promotionCategory)) {
            // set the owning side to null (unless already changed)
            if ($promotionCategory->getCategory() === $this) {
                $promotionCategory->setCategory(null);
            }
        }

        return $this;
    }
}
