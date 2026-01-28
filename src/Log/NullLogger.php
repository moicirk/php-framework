<?php

namespace PhpFramework\Log;

use Psr\Log\AbstractLogger;

class NullLogger extends AbstractLogger
{
    public function log($level, \Stringable|string $message, array $context = []): void
    {
    }
}
