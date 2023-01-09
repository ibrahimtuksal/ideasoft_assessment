<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Customer;
use App\Entity\Discount\DiscountCategory;
use App\Entity\Discount\DiscountCategoryStatus;
use App\Entity\Discount\DiscountOrder;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager)
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
            ->setName("Aletler")->setCreatedAt(new \DateTime());
        $category2 = new Category();
        $category2
            ->setName("Elektirik")->setCreatedAt(new \DateTime());

        $manager->persist($category1);
        $manager->persist($category2);

        $product1 = new Product();
        $product1
            ->setName("Black&Decker A7062 40 Parça Cırcırlı Tornavida Seti")
            ->setCategory($category1)
            ->setPrice("120.75")
            ->setStock(10)->setCreatedAt(new \DateTime());
        $product2 = new Product();
        $product2
            ->setName("Reko Mini Tamir Hassas Tornavida Seti 32'li")
            ->setCategory($category1)
            ->setPrice("49.50")
            ->setStock(10)->setCreatedAt(new \DateTime());
        $product3 = new Product();
        $product3
            ->setName("Viko Karre Anahtar - Beyaz")
            ->setCategory($category2)
            ->setPrice("11.28")
            ->setStock(10)->setCreatedAt(new \DateTime());
        $product4 = new Product();
        $product4
            ->setName("Legrand Salbei Anahtar, Alüminyum")
            ->setCategory($category2)
            ->setPrice("22.80")
            ->setStock(10)->setCreatedAt(new \DateTime());
        $product5 = new Product();
        $product5
            ->setName("Schneider Asfora Beyaz Komütatör")
            ->setCategory($category2)
            ->setPrice("12.95")
            ->setStock(10)->setCreatedAt(new \DateTime());

        $manager->persist($product1);
        $manager->persist($product2);
        $manager->persist($product3);
        $manager->persist($product4);
        $manager->persist($product5);

        // görev 2 entityleri
        $discountCategoryStatus1 = new DiscountCategoryStatus();
        $discountCategoryStatus1->setName("THE_CHEAPEST");
        $discountCategoryStatus2 = new DiscountCategoryStatus();
        $discountCategoryStatus2->setName("ONE_FREE");
        $discountCategoryStatus3 = new DiscountCategoryStatus();
        $discountCategoryStatus3->setName("CHOSEN");

        $manager->persist($discountCategoryStatus1);
        $manager->persist($discountCategoryStatus2);
        $manager->persist($discountCategoryStatus3);

        $discountOrder = new DiscountOrder();
        $discountOrder->setMinimumAmount(1000);
        $discountOrder->setPercent(10);

        $manager->persist($discountOrder);

        $discountCategory1 = (new DiscountCategory())
            ->setCategory($category2)
            ->setQuantity(6)
            ->setPercent(100)
            ->setStatus($discountCategoryStatus2);
        $discountCategory2 = (new DiscountCategory())
            ->setCategory($category1)
            ->setQuantity(2)
            ->setPercent(20)
            ->setStatus($discountCategoryStatus1);
        $manager->persist($discountCategory1);
        $manager->persist($discountCategory2);

        $manager->flush();
    }
}