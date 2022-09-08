<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Promotion;
use App\Entity\PromotionBasket;
use Doctrine\ORM\EntityManagerInterface;

class PromotionService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(PromotionBasket $basket)
    {

    }

    public function basketControl(Order $order): ?Promotion
    {
        $promotionBasket = null;
        $promotionBaskets = $this->entityManager->getRepository(PromotionBasket::class)->findAll();
        foreach ($promotionBaskets as $value){
            if ($value->getMoneyThan() <= $order->getTotalPrice()){
                if (!is_null($promotionBasket)){
                    if ($value->getMoneyThan() >= $promotionBasket->getMoneyThan()){
                        $promotionBasket = $value;
                    }
                }else{
                    $promotionBasket = $value;
                }
            }
        }

        $promotion = null;
        if ($promotionBasket instanceof PromotionBasket){
            $promotion = new Promotion();
            $promotion->setBasket($promotionBasket);
            $promotion->setOrderId($order);
        }

        return $promotion;
    }
}