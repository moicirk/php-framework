<?php

namespace PhpFramework\Console\Commands;

use PhpFramework\Application;
use PhpFramework\Console\Command;

class ListCommand extends Command
{
    public string $name = 'list';

    public string $description = 'List all available commands';

    protected function handle(): void
    {
        $message = "The help command that shows all the commands available:\n\n";
        foreach (Application::instance()->commands as $commandClass) {
            $command = new \ReflectionClass($commandClass)->newInstance();
            $message .= "* {$command->name} - {$command->description}\n";
        }

        $this->info($message);
    }
}
