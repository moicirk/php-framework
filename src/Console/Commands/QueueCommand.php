<?php

namespace PhpFramework\Console\Commands;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Table;
use PhpFramework\Console\Command;
use PhpFramework\Database\Database;
use PhpFramework\Queues\AbstractTask;

class QueueCommand extends Command
{
    public string $name = 'queue';

    public string $description = 'Run queue command';

    protected Connection $db;

    public function __construct()
    {
        $this->db = new Database()->getConnection();
    }

    protected function handle(): void
    {
        $this->info('This command will check the queues and run the tasks');
        $this->createQueuesTable();

        while (true) {
            $this->findAndExecuteTask();

            sleep(2);
        }
    }

    private function findAndExecuteTask(): void
    {
        $dql = "SELECT * FROM queues WHERE status = 'init' ORDER BY id LIMIT 1";
        $taskConfig = $this->db->executeQuery($dql)->fetchAssociative();

        if ($taskConfig === false) {
            $this->info('Nothing in a queues yet. Waiting for next iteration...');
            return;
        }

        $task = $this->parseTaskConfig($taskConfig);
        if ($this->handleTask($task)) {
            $this->db->executeQuery("UPDATE queues SET status = 'done' WHERE id = ?", [
                $taskConfig['id']
            ]);
        }

        $this->cleanUp();
    }

    private function parseTaskConfig(array $taskConfig): AbstractTask
    {
        $jsonName = json_decode($taskConfig['name']);
        $className = $jsonName->name;
        $arguments = $jsonName->arguments;

        return new $className(...$arguments);
    }

    private function handleTask(AbstractTask $task): bool
    {
        try {
            $this->info("Running task");
            $task->handle();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return false;
        }

        return true;
    }

    private function cleanUp(): void
    {
        gc_collect_cycles();
    }

    /**
     * @return void
     * @throws
     */
    private function createQueuesTable(): void
    {
        $tableName = 'queues';
        $schemaManager = $this->db->createSchemaManager();
        if (!$schemaManager->tablesExist([$tableName])) {
            $table = new Table($tableName);
            $table->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
            $table->addColumn('name', 'json', ['notnull' => true]);
            $table->addColumn('retries', 'integer', ['default' => 0]);
            $table->addColumn('result', 'json', ['default' => '{}', 'notnull' => true]);
            $table->addColumn('status', 'string', ['default' => 'init', 'notnull' => true]);

            $table->addPrimaryKeyConstraint(
                PrimaryKeyConstraint::editor()
                    ->setUnquotedName("{$tableName}_pk")
                    ->setUnquotedColumnNames('id')
                    ->create()
            );

            $table->addIndex(['status']);

            $schemaManager->createTable($table);
        }
    }
}
