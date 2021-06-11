<?php

namespace MathildeGrise\Recrutement\KataRefacto\Repositories;

use MathildeGrise\Recrutement\KataRefacto\Models\EReservation;

class EReservationRepository
{
    /**
     * @var array
     */
    private $eReservations;

    /**
     * EReservationRepository constructor.
     * @param array $eReservations
     */
    public function __construct($eReservations)
    {
        $this->eReservations = $eReservations;
    }

    /**
     * @return int
     */
    public function nextId()
    {
        return count($this->eReservations) + 1;
    }

    public function save(EReservation $eReservation)
    {
        $this->eReservations[$eReservation->getId()] = $eReservation;
    }

    public function getById(int $id)
    {
        return $this->eReservations[$id];
    }
}