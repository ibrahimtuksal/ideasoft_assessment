<?php

namespace App\Entity;

use App\Repository\PromotionCategoryStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class PromotionCategoryStatus
{

    CONST THE_CHEAPEST = 1, RANDOM = 2, CHOSEN = 3;

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
     * @ORM\OneToMany(targetEntity=PromotionCategory::class, mappedBy="status")
     */
    private $promotionCategories;

    public function __construct()
    {
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
            $promotionCategory->setStatus($this);
        }

        return $this;
    }

    public function removePromotionCategory(PromotionCategory $promotionCategory): self
    {
        if ($this->promotionCategories->removeElement($promotionCategory)) {
            // set the owning side to null (unless already changed)
            if ($promotionCategory->getStatus() === $this) {
                $promotionCategory->setStatus(null);
            }
        }

        return $this;
    }
}
