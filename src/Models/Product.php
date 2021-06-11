<?php

namespace MathildeGrise\Recrutement\KataRefacto\Models;

class Product
{
    /**
     * @var string
     */
    private $sku;
    /**
     * @var int
     */
    private $price;

    /**
     * Product constructor.
     * @param string $sku
     * @param int $price
     */
    public function __construct(string $sku, int $price)
    {
        $this->sku = $sku;
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }
}