<?php

namespace PhpFramework\Console\Commands\Migration;

use Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand;

class MigrationUpdateCommand extends AbstractMigrationCommand
{
    public string $name = 'migration:update';

    public string $description = 'Update a migration class';

    protected function handle(): void
    {
        $this->executeSymfonyCommand(
            'orm:schema-tool:update',
            UpdateCommand::class
        );
    }
}
