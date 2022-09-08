<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItems;
use App\Entity\Promotion;
use App\Entity\PromotionBasket;
use App\Entity\PromotionCategory;
use App\Entity\PromotionCategoryStatus;
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

    public function categoryControl(Order $order)
    {
        $categories = [];
        $productPrice = 0;
        foreach ($order->getOrderItems() as $orderItem){
            $categoryId = $orderItem->getProduct()->getCategory()->getId();
            $categories[$categoryId] = isset($categories[$categoryId]) ? $categories[$categoryId] + $orderItem->getQuantity() : $orderItem->getQuantity();
        }
        $categoryIds = array_keys($categories);
        $promotionCategories = $this->entityManager->getRepository(PromotionCategory::class)->findBy(['category' => $categoryIds]);
        /** @var PromotionCategory $promotionCategory */
        foreach ($promotionCategories as $promotionCategory){

            if($promotionCategory->getQuantity() <= $categories[$promotionCategory->getCategory()->getId()]){
                if($promotionCategory->getStatus()->getId() == PromotionCategoryStatus::ONE_FREE){
                    $productPrice = $productPrice + $order->getOrderItems()[0]->getProduct()->getPrice();

                }else if($promotionCategory->getStatus()->getId() == PromotionCategoryStatus::THE_CHEAPEST){
                    $cheapest = null;
                    /** @var OrderItems $orderItem */
                    foreach ($order->getOrderItems() as $orderItem){
                        if (!is_null($cheapest)){
                            if ($cheapest->getProduct()->getPrice() > $orderItem->getProduct()->getPrice()){
                                $cheapest = $orderItem;
                            }
                        }else {
                            $cheapest = $orderItem;
                        }
                    }
                    $productPrice += $cheapest->getProduct()->getPrice();
                }
            }
        }
        return $productPrice;
    }
}
