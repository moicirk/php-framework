<?php

namespace PhpFramework\Console;

abstract class Command
{
    use LogTrait;

    public string $name = 'app';
    public string $description = 'Command class';

    protected array $arguments = [];

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
