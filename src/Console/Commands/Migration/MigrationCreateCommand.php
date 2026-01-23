<?php

namespace PhpFramework\Console\Commands\Migration;

use Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand;

class MigrationCreateCommand extends AbstractMigrationCommand
{
    public string $name = 'migration:create';

    public string $description = 'Create a migration class';

    protected function handle(): void
    {
        $this->executeSymfonyCommand(
            'orm:schema-tool:create',
            CreateCommand::class
        );
    }
}
