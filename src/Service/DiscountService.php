<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Discount\Discount;
use App\Entity\Discount\DiscountCategory;
use App\Entity\Discount\DiscountCategoryStatus;
use App\Entity\Discount\DiscountOrder;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class DiscountService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Order $order
     * @return array
     */
    public function apply(Order $order): array
    {
        $discounts = [];
        $discounts['category'] = $this->checkCategory($order);
        $discounts['order'] = $this->checkOrder($order);
        return $discounts;
    }

    /**
     * @param Order $order
     * @return array
     */
    public function checkOrder(Order $order): array
    {
        // sipariş için indirimleri çekiyor
        $discountOrders = $this->em->getRepository(DiscountOrder::class)->findAll();

        $discounts = [];
        /**
         * @var DiscountOrder $discountOrder
         * Sipariş tabanlı bir indirim tanımlanmışsa döngü çalışır
         */
        foreach ($discountOrders as $discountOrder){
            // minimum tutar sipariş tutarından küçükse
            if ($discountOrder->getMinimumAmount() < $order->getTotal()){
                // yüzdelik indirimi hesapla
                $discountedPrice = $order->getTotal() * $discountOrder->getPercent() / 100;
                $discounts[] = $this->create($order, $discountedPrice, $discountOrder);
            }
        }
        return $discounts;
    }

    /**
     * @param Order $order
     * @return array
     */
    public function checkCategory(Order $order): array
    {
        $items = $this->em->getRepository(OrderItem::class)->findBy(['orderId' => $order]);

        // Sipariş öğeleri için kategorileri alın
        $orderItemCategories = $this->getOrderItemsCategories($items);

        // İndirim kontrolü için indirim kategorilerini alın
        $discountCategories = $this->getDiscountCategoriesByCategoriesId(array_keys($orderItemCategories));

        // Kategoriler için indirimi uygula
        return $this->applyDiscountFotCategories($discountCategories, $order);
    }

    /**
     * @param DiscountCategory $discountCategory
     * @param Order $order
     * @return float
     */
    public function applyDiscountFotCategory(DiscountCategory $discountCategory, Order $order): float
    {
        $orderItems = $this->em->getRepository(OrderItem::class)->findBy(['orderId' => $order]);
        // siparişte kaç adet indirim objesine ait kategori var hesapla
        $quantity = $this->categoryQuantityCheckForOrder($orderItems, $discountCategory->getCategory()->getId());
        // kategori sayısı belirlenmiş olandan büyükse veya eşitse işleme devam et
        if ($quantity >= $discountCategory->getQuantity()){
            // indirime dahil olan ürünleri al
            $discountProducts = $this->filterProductsByCategory($orderItems, $discountCategory->getCategory());
            switch ($discountCategory->getStatus()->getName()) {
                case DiscountCategoryStatus::THE_CHEAPEST:
                    // en ucuz ürünü bul
                    $cheapestProduct = $this->findMinAmountForProducts($discountProducts);
                    // indirim tutarı döndür
                    return $cheapestProduct->getPrice();
                    break;
                case DiscountCategoryStatus::ONE_FREE:
                    /** @var Product $freeProduct */
                    $freeProduct = $discountProducts[array_rand($discountProducts)];
                    // indirim tutarı döndür
                    return $freeProduct->getPrice();
                    break;
                case DiscountCategoryStatus::CHOSEN:
                    // Open for Development
                    break;
                default:
                    throw new UnprocessableEntityHttpException('DISCOUNT_CATEGORY_STATUS_NOT_FOUND');
            }
        }
        return 0.0;
    }

    /**
     * @param array $discountCategories
     * @param Order $order
     * @return array
     */
    public function applyDiscountFotCategories(array $discountCategories, Order $order): array
    {
        $discounts = [];
        /** @var DiscountCategory $discountCategory */
        foreach ($discountCategories as $discountCategory){
            $discountAmount = $this->applyDiscountFotCategory($discountCategory, $order);
            $discounts[] = $this->create($order, $discountAmount, null, $discountCategory);
        }
        return $discounts;
    }

    /**
     * @param $orderItems
     * @return array
     */
    public function getOrderItemsCategories($orderItems): array
    {
        $categories = [];

        /** @var OrderItem $orderItem */
        foreach ($orderItems as $orderItem){
            $categories[$orderItem->getCategory()->getId()] = $orderItem->getCategory();
        }

        return $categories;
    }

    /**
     * @param array $categoryIds
     * @return array
     */
    public function getDiscountCategoriesByCategoriesId(array $categoryIds): array
    {
        return $this->em->getRepository(DiscountCategory::class)->findBy(['category' => $categoryIds]);
    }

    /**
     * @param array $orderItems
     * @param int $category
     * @return int
     */
    public function categoryQuantityCheckForOrder(array $orderItems, int $category): int
    {
        $quantity = 0;

        /** @var OrderItem $orderItem */
        foreach ($orderItems as $orderItem){
            for ($i = 0; $i < $orderItem->getQuantity(); $i++){
                $quantity++;
            }
        }
        return $quantity;
    }

    /**
     * @param array $items
     * @param Category $category
     * @return array
     */
    private function filterProductsByCategory(array $items, Category $category): array
    {
        $filteredProducts = [];

        /** @var OrderItem $item */
        foreach ($items as $item){
            if ($item->getCategory() instanceof $category){
                $filteredProducts[] = $item->getProduct();
            }
        }

        return $filteredProducts;
    }

    /**
     * @param array $discountProducts
     * @return Product
     */
    public function findMinAmountForProducts(array $discountProducts): Product
    {
        // En küçük sayıyı tutmak için ilk veriyi başlangıç olarak ayarla
        $theCheapestProduct = $discountProducts[0];

        /** @var Product $product */
        foreach ($discountProducts as $product){
            if ($product->getPrice() < $theCheapestProduct->getPrice()){
                $theCheapestProduct = $product;
            }
        }

        return $theCheapestProduct;
    }

    /**
     * @param Order $order
     * @param float $amount
     * @param null $discountOrder
     * @param null $discountCategory
     * @return Discount
     */
    public function create(Order $order, float $amount, $discountOrder = null, $discountCategory = null): Discount
    {
        $discount = (new Discount())
            ->setOrders($order)
            ->setAmount($amount)
            ->setDCategory($discountCategory)
            ->setDOrder($discountOrder)
            ->setCreatedAt(new \DateTime());

        $this->em->persist($discount);
        $this->em->flush();
        return $discount;
    }
}