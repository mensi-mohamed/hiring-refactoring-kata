<?php

namespace MathildeGrise\Recrutement\KataRefacto\Models;

class EReservation
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var int
     */
    private $storeId;
    /**
     * @var string
     */
    private $productSku;
    /**
     * @var int
     */
    private $price;
    /**
     * @var int
     */
    private $customerId;

    /**
     * EReservation constructor.
     * @param int $id
     * @param int $storeId
     * @param string $productSku
     * @param int $price
     * @param int $customerId
     */
    public function __construct($id, $storeId, $productSku, $price, $customerId)
    {
        $this->id = $id;
        $this->storeId = $storeId;
        $this->productSku = $productSku;
        $this->price = $price;
        $this->customerId = $customerId;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @return string
     */
    public function getProductSku()
    {
        return $this->productSku;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
}