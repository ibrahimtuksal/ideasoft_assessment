<?php

namespace App\Entity;

use App\Repository\PromotionBasketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class PromotionBasket
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
    private $moneyThan;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $percent;

    /**
     * @ORM\OneToMany(targetEntity=Promotion::class, mappedBy="basket")
     */
    private $promotions;

    public function __construct()
    {
        $this->promotions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMoneyThan(): ?string
    {
        return $this->moneyThan;
    }

    public function setMoneyThan(string $moneyThan): self
    {
        $this->moneyThan = $moneyThan;

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
            $promotion->setBasket($this);
        }

        return $this;
    }

    public function removePromotion(Promotion $promotion): self
    {
        if ($this->promotions->removeElement($promotion)) {
            // set the owning side to null (unless already changed)
            if ($promotion->getBasket() === $this) {
                $promotion->setBasket(null);
            }
        }

        return $this;
    }
}
