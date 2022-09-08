<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItems;
use App\Entity\Product;
use App\Type\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class OrderService
{
    private OrderType $orderType;
    private EntityManagerInterface $entityManager;
    private PromotionService $promotionService;

    public function __construct(OrderType $orderType, EntityManagerInterface $entityManager, PromotionService $promotionService)
    {
        $this->orderType = $orderType;
        $this->entityManager = $entityManager;
        $this->promotionService = $promotionService;
    }

    /**
     * @param $data
     * @return array
     */
    public function create($data): array
    {
        $orderType = $this->orderType;
        try {
            $orderType->importFromRequest($data);
        }catch (\Exception $e)
        {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        $order = new Order();
        $order->setCustomer($orderType->customer);
        $order->setCreatedAt(new \DateTime());

        $items = [];
        $totalPrice = 0;
        foreach ($orderType->items as $item){
            $product = $this->entityManager->getRepository(Product::class)->find($item['productId']);

            if (!$product instanceof Product){
                throw new UnprocessableEntityHttpException("PRODUCT_NOT_FOUND");
            }

            if ($product->getStock() >= $item['quantity']){
                $product->setStock($product->getStock() - $item['quantity']);
            }else{
                throw new UnprocessableEntityHttpException("PRODUCT_STOCK_NOT_FOUND");
            }

            $productTotalPrice = $product->getPrice() * $item['quantity'];
            $totalPrice += $productTotalPrice;

            $orderItem = new OrderItems();
            $order->addOrderItem($orderItem);
            $orderItem
                ->setProduct($product)
                ->setQuantity($item['quantity']);

            $items[] = [
                "productId"=> $product->getId(),
                "quantity"=> $item['quantity'],
                "unitPrice"=> $product->getPrice(),
                "total"=> $productTotalPrice
            ];

            $this->entityManager->persist($orderItem);
        }
        $order->setTotalPrice($totalPrice);

        $orderType->customer->setRevenue($orderType->customer->getRevenue() + $totalPrice);

        $promotion = $this->promotionService->basketControl($order);
        if (!is_null($promotion)){
            $this->entityManager->persist($promotion);
        }
        $order
            ->setDiscountPrice(is_null($promotion) ? $promotion : $totalPrice - ( ($totalPrice / 100) * $promotion->getBasket()->getPercent() ));

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return
            [
                "id" => $order->getId(),
                "customerId"=> $orderType->customer->getId(),
                "items"=> $items,
                "totalPrice" => $order->getTotalPrice(),
                "discountPrice" => $order->getDiscountPrice(),
                "promotionId" => is_null($promotion) ? $promotion : $promotion->getId()
            ];
    }

    public function delete(Order $order): array
    {
        $order->setDeletedAt(new \DateTime());

        $this->entityManager->flush();

        return [
            'success' => true,
            'message' => 'ORDER_DELETED',
            'response' => [
                'id' => $order->getId(),
                'createdAt' => $order->getCreatedAt()->format("Y-m-d H:i"),
            ]
        ];
    }

    public function list(Order $order): array
    {
        $item = [];

        /** @var OrderItems $orderItem */
        foreach ($order->getOrderItems() as $orderItem){
            $item[] = [
                'product' => $orderItem->getProduct()->getName(),
                'unitPrice' => $orderItem->getProduct()->getPrice(),
                'quantity' => $orderItem->getQuantity(),
                'total' => $orderItem->getProduct()->getPrice() * $orderItem->getQuantity()
            ];
        }

        return [
            'id' => $order->getId(),
            'createdAt' => $order->getCreatedAt()->format("Y-m-d H:i"),
            'customer' => [
                'id' => $order->getCustomer()->getId(),
                'name' => $order->getCustomer()->getName(),
            ],
            'items' => $item,
            'total' => $order->getTotalPrice()
        ];
    }
}