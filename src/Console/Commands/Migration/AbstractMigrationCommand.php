<?php

namespace PhpFramework\Console\Commands\Migration;

use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use PhpFramework\Console\Command;
use PhpFramework\Database\Database;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

abstract class AbstractMigrationCommand extends Command
{
    protected function executeSymfonyCommand(string $script, string $commandClass): void
    {
        $db = new Database();
        $input = new ArrayInput(['command' => $script]);
        $output = new ConsoleOutput();
        $command = new $commandClass(
            new SingleManagerProvider($db->getEntityManager())
        );

        $application = new Application();
        $application->addCommand($command);
        $application->setAutoExit(false);

        $application->run($input, $output);
    }
}
