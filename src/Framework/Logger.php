<?php

namespace MathildeGrise\Recrutement\KataRefacto\Framework;

class Logger
{
    /**
     * log level used for logging E-reservation steps
     */
    const INFO_LOG_LEVEL = 'INFO';

    /**
     * @param string $message
     * @param int $logLevel
     */
    public function log($message, $logLevel)
    {
        echo($logLevel . " : " . $message . "\n");
    }
}