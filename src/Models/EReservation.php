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

    /**
     * @param int $id
     *
     * @return EReservation
     */
    public function setId(int $id): EReservation
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param int $storeId
     *
     * @return EReservation
     */
    public function setStoreId(int $storeId): EReservation
    {
        $this->storeId = $storeId;

        return $this;
    }

    /**
     * @param string $productSku
     *
     * @return EReservation
     */
    public function setProductSku(string $productSku): EReservation
    {
        $this->productSku = $productSku;

        return $this;
    }

    /**
     * @param int $price
     *
     * @return EReservation
     */
    public function setPrice(int $price): EReservation
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @param int $customerId
     *
     * @return EReservation
     */
    public function setCustomerId(int $customerId): EReservation
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    public static function create(
        int $id,
        int $storeId,
        int|string $productId,
        int $price,
        int $customerId
    ): self
    {
        return (new self())
            ->setId($id)
            ->setStoreId($storeId)
            ->setProductSku($productId)
            ->setPrice($price)
            ->setCustomerId($customerId)
;
    }
}