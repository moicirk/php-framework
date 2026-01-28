<?php

namespace PhpFramework\Log;

class ConsoleLogger extends Logger
{
    protected function writeToLog($level, \Stringable|string $message, array $context = []): void
    {
        $color = match($level) {
            'error' => "\033[31m",
            'info'  => "\033[32m",
            'debug' => "\033[36m",
            default => "\033[0m"
        };
        $reset = "\033[0m";

        fwrite(STDOUT, $color . $message . $reset);
    }
}
