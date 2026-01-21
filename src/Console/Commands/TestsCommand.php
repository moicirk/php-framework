<?php

namespace PhpFramework\Console\Commands;

use PhpFramework\Console\Command;

class TestsCommand extends Command
{
    public string $name = 'tests';

    public string $description = 'Run the PHP tests';

    protected function handle(): void
    {
        $output = shell_exec('./vendor/bin/phpunit');
        echo $output;
    }
}
