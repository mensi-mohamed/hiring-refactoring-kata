<?php

namespace MathildeGrise\Recrutement\KataRefacto\Models;

class Customer
{
    /**
     * @var int
     */
    private $id;

    /**
     * Customer constructor.
     * @param int $id
     * @param string $email
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}