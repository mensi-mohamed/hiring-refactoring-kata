<?php

namespace MathildeGrise\Recrutement\KataRefacto\Framework;

class Response
{
    /**
     * @var int
     */
    private $code;
    /**
     * @var array
     */
    private $data;

    /**
     * @param int $code
     * @return Response
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}