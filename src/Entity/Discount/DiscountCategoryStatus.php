<?php

namespace App\Entity\Discount;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class DiscountCategoryStatus
{
    CONST THE_CHEAPEST = 'THE_CHEAPEST', ONE_FREE = 'ONE_FREE', CHOSEN = 'CHOSEN';
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
     * @ORM\OneToMany(targetEntity=DiscountCategory::class, mappedBy="status")
     */
    private $discountCategories;

    public function __construct()
    {
        $this->discountCategories = new ArrayCollection();
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
            $discountCategory->setStatus($this);
        }

        return $this;
    }

    public function removeDiscountCategory(DiscountCategory $discountCategory): self
    {
        if ($this->discountCategories->removeElement($discountCategory)) {
            // set the owning side to null (unless already changed)
            if ($discountCategory->getStatus() === $this) {
                $discountCategory->setStatus(null);
            }
        }

        return $this;
    }
}
