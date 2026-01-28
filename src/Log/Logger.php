<?php

namespace PhpFramework\Log;

use Psr\Log\AbstractLogger;

abstract class Logger extends AbstractLogger
{
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->writeToLog($level, $message, $context);
    }

    abstract protected function writeToLog($level, \Stringable|string $message, array $context = []): void;
}
