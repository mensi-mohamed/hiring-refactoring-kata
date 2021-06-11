<?php

namespace MathildeGrise\Recrutement\KataRefacto\Framework;

use MathildeGrise\Recrutement\KataRefacto\Models\Store;

class ApplicationContext
{
    /**
     * @var $this
     */
    protected static $instance = null;
    /**
     * @var Store
     */
    public static $currentStore;
    /**
     * @var array
     */
    public static $config;

    /**
     * @return $this
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @return Store
     */
    public function getCurrentStore()
    {
        return self::$currentStore;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return self::$config;
    }
}