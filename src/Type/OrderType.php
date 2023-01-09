<?php

namespace App\Type;

use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;

class OrderType
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(Customer::class)
     * @var Customer
     */
    public Customer $customer;

    /**
     * @Assert\NotBlank
     * @Assert\Type("array")
     * @var array
     */
    public array $items;

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function importFromRequest(array $data)
    {
        $customer = $this->em->getRepository(Customer::class)->find($data['customer']);
        $this->customer = $customer;
        $this->items = $data['items'];
    }
}