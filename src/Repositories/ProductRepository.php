<?php

namespace MathildeGrise\Recrutement\KataRefacto\Repositories;

class ProductRepository
{
    /**
     * @var array
     */
    private $products;

    /**
     * ProductRepository constructor.
     * @param array $products
     */
    public function __construct($products)
    {
        $this->products = $products;
    }

    /**
     * @param string $sku
     * @param int $storeId
     */
    public function getProductFromSkuByStore($sku, $storeId)
    {
        return $this->products[$storeId][$sku];
    }
}