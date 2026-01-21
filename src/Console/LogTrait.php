<?php

namespace PhpFramework\Console;

trait LogTrait
{
    /**
     * Write info message
     *
     * @param string $message
     * @return void
     */
    protected function info(string $message): void
    {
        $this->writeLn($message);
    }

    /**
     * Write error message
     *
     * @param string $message
     * @return void
     */
    protected function error(string $message): void
    {
        $this->writeLn($message);
    }

    private function writeLn(string $message): void
    {
        echo $message . PHP_EOL;
    }
}
