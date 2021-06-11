<?php

namespace MathildeGrise\Recrutement\KataRefacto\Repositories;

class CustomerRepository
{
    /**
     * @var array
     */
    private $customers;

    /**
     * CustomerRepository constructor.
     * @param array $customers
     */
    public function __construct($customers)
    {
        $this->customers = $customers;
    }

    /**
     * @param int $id
     */
    public function getById($id)
    {
        return $this->customers[$id];
    }
}