<?php

namespace App\Service;

use App\Entity\Discount\Discount;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Type\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class OrderService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var DiscountService
     */
    private DiscountService $discountService;
    /**
     * @var CustomerService
     */
    private CustomerService $customerService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param DiscountService $discountService
     * @param CustomerService $customerService
     */
    public function __construct(EntityManagerInterface $entityManager,
                                DiscountService        $discountService,
                                CustomerService        $customerService)
    {
        $this->entityManager = $entityManager;
        $this->discountService = $discountService;
        $this->customerService = $customerService;
    }

    /**
     * @param $data
     * @return array
     */
    public function create($data): array
    {
        $orderType = new OrderType($this->entityManager);
        $orderType->importFromRequest($data);

        $order = (new Order())
            ->setCustomer($orderType->customer)
            ->setCreatedAt(new \DateTime())
            ->setTotal(0)
            ->setDiscountPrice(null);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $itemInfo = [];
        $totalPrice = 0;
        foreach ($orderType->items as $item){
            // gerekli kontrolleri sağlayarak sipariş itemi oluştur
            $orderItem = $this->createOrderItem($order, $item);
            $this->entityManager->persist($orderItem);

            // ürünün miktarına göre fiyatını hesapla
            $productTotalPrice = $this->calculateProductPriceByQuantity($orderItem->getProduct(), $item['quantity']);

            // ürünün miktarına göre apply tutarını güncelle
            $totalPrice += $productTotalPrice;

            $itemInfo[] = [
                "productId" => $orderItem->getProduct()->getId(),
                "quantity" => $item['quantity'],
                "unitPrice" => $orderItem->getProduct()->getPrice(),
                "total"=> $productTotalPrice
            ];
        }

        // sipariş tutarı
        $order->setTotal($totalPrice);
        $this->entityManager->flush();
        // müşteri toplam harcamayı güncelle
        $this->customerService->updateRevenueForCustomer($orderType->customer, $totalPrice);
        // halihazırda aktif olan indirimleri kontrol et var ise indirimleri uygula
        $discountedAmount = $this->applyDiscount($this->discountService->apply($order));
        // indirimi sipariş objesine set et
        $order->setDiscountPrice($discountedAmount);
        // işlemleri veritabanına yansıt
        $this->entityManager->flush();
        return
            [
                "id" => $order->getId(),
                "customerId"=> $orderType->customer->getId(),
                "items"=> $itemInfo,
                "totalPrice" => $order->getTotal(),
                "discountPrice" => $order->getDiscountPrice(),
            ];
    }

    /**
     * @param Order $order
     * @param $item
     * @return OrderItem
     */
    public function createOrderItem(Order $order, $item): OrderItem
    {
        /** @var Product $product */
        $product = $this->entityManager->getRepository(Product::class)->find($item['productId']);

        if (!$product instanceof Product){
            throw new UnprocessableEntityHttpException("PRODUCT_NOT_FOUND");
        }

        // stok kontrolü yapılarak stoktan düşülebilir mi diye kontrol edip stoktan düşüyor, eğer stok yok ise hata mesajı fırlatıyor
        if ($this->stockControlForQuantity($product, $item['quantity'])){
            $product->setStock($product->getStock() - $item['quantity']);
        }else {throw new UnprocessableEntityHttpException("PRODUCT_STOCK_NOT_FOUND");}

        //üründen kaç adet istediyse fiyatı çarpıp toplam fiyatı alıyor
        $productTotalPrice = $this->calculateProductPriceByQuantity($product, $item['quantity']);

        return (new OrderItem())
            ->setProduct($product)
            ->setCreatedAt(new \DateTime())
            ->setCategory($product->getCategory())
            ->setQuantity($item['quantity'])
            ->setOrderId($order)
            ->setTotal($productTotalPrice)
            ->setUnitPrice( $product->getPrice() );
    }

    /**
     * @param Product $product
     * @param $quantity
     * @return bool
     */
    public function stockControlForQuantity(Product $product, $quantity): bool
    {
        if ($product->getStock() >= $quantity){
            return true;
        }
        return false;
    }

    /**
     * @param Product $product
     * @param $quantity
     * @return float
     */
    public function calculateProductPriceByQuantity(Product $product, $quantity): float
    {
        return $product->getPrice() * $quantity;
    }

    /**
     * @param int $order
     * @return array
     */
    public function delete(int $order): array
    {
        $order = $this->entityManager->getRepository(Order::class)->find($order);
        if (!$order instanceof Order){
            throw new UnprocessableEntityHttpException('ORDER_NOT_FOUND');
        }
        $order->setDeletedAt(new \DateTime());

        $this->entityManager->flush();

        return [
            'success' => true,
            'message' => 'ORDER_DELETED',
            'response' => [
                'id' => $order->getId(),
                'deletedAt' => $order->getCreatedAt()->format("Y-m-d H:i"),
            ]
        ];
    }

    /**
     * @param int $order
     * @return array
     */
    public function list(int $order): array
    {
        $order = $this->entityManager->getRepository(Order::class)->find($order);
        if (!$order instanceof Order){
            throw new UnprocessableEntityHttpException('ORDER_NOT_FOUND');
        }

        $item = [];

        /** @var OrderItem $orderItem */
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
            'totalPrice' => $order->getTotal(),
            'discountPrice' => $order->getDiscountPrice()
        ];
    }

    /**
     * @param array $discounts
     * @return float|null
     */
    public function applyDiscount(array $discounts): ?float
    {
        $categoryDiscountedAmount = $this->applyCategoryDiscount($discounts['category']);
        $orderDiscountedAmount = $this->applyOrderDiscount($discounts['order']);

        return $categoryDiscountedAmount + $orderDiscountedAmount;
    }

    /**
     * @param array $discounts
     * @return float|null
     */
    public function applyCategoryDiscount(array $discounts): ?float
    {
        $discountedAmount = 0.0;
        /** @var Discount $discount */
        foreach ($discounts as $discount)
        {
            $discountedAmount += $discount->getAmount();
        }
        return $discountedAmount;
    }

    /**
     * @param array $discounts
     * @return float|null
     */
    public function applyOrderDiscount(array $discounts): ?float
    {
        $discountedAmount = 0.0;
        /** @var Discount $discount */
        foreach ($discounts as $discount)
        {
            $discountedAmount += $discount->getAmount();
        }
        return $discountedAmount;
    }
}