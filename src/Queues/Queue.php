<?php

namespace PhpFramework\Queues;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Table;
use PhpFramework\Database\Database;
use PhpFramework\Exceptions\Queues\QueueException;

/**
 * TODO: Send the db in the QueueTable class
 */
class Queue implements QueueInterface
{
    private Connection $connection;

    private string $tableName = 'queues';

    public function __construct() {
        $this->connection = new Database()->getConnection();
    }

    public static function dispatch(AbstractTask $task): void
    {
        $queue = new self();
        $queue->push($task);
    }

    public function push(AbstractTask $task): void
    {
        $this->connection->executeQuery("INSERT INTO queues (name) VALUES (?)", [
            serialize($task)
        ]);
    }

    public function pop(): ?AbstractTask
    {
        $taskConfig = $this->connection
            ->executeQuery("SELECT * FROM queues ORDER BY id LIMIT 1")
            ->fetchAssociative();

        if ($taskConfig === false) {
            return null;
        }

        /** @var AbstractTask $task */
        $task = unserialize($taskConfig['name']);
        $task->id = $taskConfig['id'];

        return $task;
    }

    public function deleteTask(AbstractTask $task): void
    {
        if ($task->id === null) {
            throw new QueueException('The task has no $id');
        }

        $this->connection->executeQuery("DELETE FROM queues WHERE id = ?", [
            $task->id
        ]);
    }

    public function createQueuesTable(): void
    {
        $table = new Table($this->tableName);
        $table->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('name', 'string', ['notnull' => true]);
        $table->addColumn('retries', 'integer', ['default' => 0]);
        $table->addColumn('result', 'json', ['default' => '{}', 'notnull' => true]);

        $table->addPrimaryKeyConstraint(
            PrimaryKeyConstraint::editor()
                ->setUnquotedName("{$this->tableName}_pk")
                ->setUnquotedColumnNames('id')
                ->create()
        );

        $this->connection
            ->createSchemaManager()
            ->createTable($table);
    }

    public function hasQueueTable(): bool
    {
        return $this->connection
            ->createSchemaManager()
            ->tablesExist([$this->tableName]);
    }
}
