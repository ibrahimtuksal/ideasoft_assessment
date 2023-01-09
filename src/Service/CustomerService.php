<?php

namespace App\Service;

use App\Entity\Customer;

class CustomerService
{
    /**
     * @param Customer $customer
     * @param $revenue
     * @return void
     */
    public function updateRevenueForCustomer(Customer $customer, $revenue)
    {
        $customer->setRevenue($customer->getRevenue() + $revenue);
    }
}