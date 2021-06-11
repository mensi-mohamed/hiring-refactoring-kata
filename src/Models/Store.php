<?php

namespace MathildeGrise\Recrutement\KataRefacto\Models;

class Store
{
    /**
     * @var int
     */
    private $id;

    /**
     * Store constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }


    /**
     * @return int;
     */
    public function getId()
    {
        return $this->id;
    }
}