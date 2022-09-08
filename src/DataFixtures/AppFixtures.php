<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\PromotionBasket;
use App\Entity\PromotionCategory;
use App\Entity\PromotionCategoryStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $customer1 = new Customer();
        $customer1
            ->setName("Türker Jöntürk")
            ->setSince(new \DateTime())
            ->setRevenue("492.12");
        $customer2 = new Customer();
        $customer2
            ->setName("Kaptan Devopuz")
            ->setSince(new \DateTime())
            ->setRevenue("1505.95");
        $customer3 = new Customer();
        $customer3
            ->setName("İsa Sonuyumaz")
            ->setSince(new \DateTime())
            ->setRevenue("0.00");

        $manager->persist($customer1);
        $manager->persist($customer2);
        $manager->persist($customer3);

        $category1 = new Category();
        $category1
            ->setName("Aletler");
        $category2 = new Category();
        $category2
            ->setName("Elektirik");

        $manager->persist($category1);
        $manager->persist($category2);

        $product1 = new Product();
        $product1
            ->setName("Black&Decker A7062 40 Parça Cırcırlı Tornavida Seti")
            ->setCategory($category1)
            ->setPrice("120.75")
            ->setStock(10);
        $product2 = new Product();
        $product2
            ->setName("Reko Mini Tamir Hassas Tornavida Seti 32'li")
            ->setCategory($category1)
            ->setPrice("49.50")
            ->setStock(10);
        $product3 = new Product();
        $product3
            ->setName("Viko Karre Anahtar - Beyaz")
            ->setCategory($category2)
            ->setPrice("11.28")
            ->setStock(10);
        $product4 = new Product();
        $product4
            ->setName("Legrand Salbei Anahtar, Alüminyum")
            ->setCategory($category2)
            ->setPrice("22.80")
            ->setStock(10);
        $product5 = new Product();
        $product5
            ->setName("Schneider Asfora Beyaz Komütatör")
            ->setCategory($category2)
            ->setPrice("12.95")
            ->setStock(10);

        $manager->persist($product1);
        $manager->persist($product2);
        $manager->persist($product3);
        $manager->persist($product4);
        $manager->persist($product5);

        // görev 2 entityleri
        $promotionCategoryStatus1 = new PromotionCategoryStatus();
        $promotionCategoryStatus1->setName("THE_CHEAPEST");
        $promotionCategoryStatus2 = new PromotionCategoryStatus();
        $promotionCategoryStatus2->setName("RANDOM");
        $promotionCategoryStatus3 = new PromotionCategoryStatus();
        $promotionCategoryStatus3->setName("CHOSEN");

        $manager->persist($promotionCategoryStatus1);
        $manager->persist($promotionCategoryStatus2);
        $manager->persist($promotionCategoryStatus3);

        $promotionBasket = new PromotionBasket();
        $promotionBasket->setMoneyThan("1000");
        $promotionBasket->setPercent("10");

        $manager->persist($promotionBasket);

        $promotionCategory1 = new PromotionCategory();
        $promotionCategory1
            ->setCategory($category2)
            ->setQuantity(6)
            ->setPercent("100")
            ->setStatus($promotionCategoryStatus2)
            ->setHowManyProducts(1);
        $promotionCategory2 = new PromotionCategory();
        $promotionCategory2
            ->setCategory($category1)
            ->setQuantity(2)
            ->setPercent("20")
            ->setStatus($promotionCategoryStatus1)
            ->setHowManyProducts(1);

        $manager->persist($promotionCategory1);
        $manager->persist($promotionCategory2);

        $manager->flush();
    }
}
