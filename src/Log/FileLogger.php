<?php

namespace PhpFramework\Log;

use PhpFramework\Application;

class FileLogger extends Logger
{
    public function __construct(
        private readonly string $logFile = 'app.log'
    ) {
    }

    public function writeToLog($level, \Stringable|string $message, array $context = []): void
    {
        //TODO: File context needs to be extended to support classes, functions, etc...
        $fileContext = empty($context) ? null : json_encode($context);

        $timestamp = date('Y-m-d H:i:s');
        $formatted = "[$timestamp] [$level] $message $fileContext" . PHP_EOL;

        $rootPath = Application::instance()->getBasePath();
        $handle = fopen("$rootPath/logs/{$this->logFile}", "a");
        if ($handle) {
            if (flock($handle, LOCK_EX)) {
                fwrite($handle, $formatted);
                flock($handle, LOCK_UN);
            }

            fclose($handle);
        }

        var_dump($formatted);
        exit;
    }
}
