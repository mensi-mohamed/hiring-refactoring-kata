<?php

namespace MathildeGrise\Recrutement\KataRefacto\Framework;

class Application_ServiceLocator
{
    public static $services = [];
    /**
     * @param string $service
     * @return mixed
     */
    public static function get($service)
    {
        return self::$services[$service];
    }
}