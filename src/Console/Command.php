<?php

namespace PhpFramework\Console;

use PhpFramework\Application;
use Psr\Log\LoggerInterface;

abstract class Command
{
    public string $name = 'app';
    public string $description = 'Command class';

    protected array $arguments = [];

    protected LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = Application::instance()
            ->getContainer()
            ->get(LoggerInterface::class);
    }

    /**
     * Run the console class
     *
     * @param array $arguments
     * @return void
     */
    public function run(array $arguments = []): void
    {
        $this->arguments = $arguments;
        $this->handle();
    }

    abstract protected function handle(): void;
}
