<?php

namespace MathildeGrise\Recrutement\KataRefacto\Framework;

class Logger
{
    /**
     * @param string $message
     * @param int $logLevel
     */
    public function log($message, $logLevel)
    {
        echo($logLevel . " : " . $message . "\n");
    }
}