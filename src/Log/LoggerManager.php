<?php

namespace PhpFramework\Log;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class LoggerManager implements LoggerAwareInterface, LoggerInterface
{
    use LoggerTrait;

    private array $logs = [];

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logs[] = $logger;
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        /** @var Logger $logger */
        foreach ($this->logs as $logger) {
            $logger->log($level, $message, $context);
        }
    }
}
