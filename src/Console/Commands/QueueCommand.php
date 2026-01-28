<?php

namespace PhpFramework\Console\Commands;

use PhpFramework\Console\Command;
use PhpFramework\Queues\AbstractTask;
use PhpFramework\Queues\Queue;

class QueueCommand extends Command
{
    public string $name = 'queue';

    public string $description = 'Run queue command';

    private Queue $queue;

    public function __construct()
    {
        parent::__construct();
        $this->queue = new Queue();
    }

    protected function handle(): void
    {
        $this->logger->info('This command will check the queues and run the tasks');

        if (!$this->queue->hasQueueTable()) {
            $this->queue->createQueuesTable();
        }

        while (true) {
            $this->findAndExecuteTask();

            sleep(2);
        }
    }

    private function findAndExecuteTask(): void
    {
        $task = $this->queue->pop();
        if ($task === null) {
            $this->logger->info('Nothing in a queues yet. Waiting for next iteration...');
            return;
        }

        if ($this->handleTask($task)) {
            $this->queue->deleteTask($task);
        }

        $this->cleanUp();
    }

    private function handleTask(AbstractTask $task): bool
    {
        try {
            $className = get_class($task);
            $this->logger->info("Starting task {$className}");
            $task->handle();
            $this->logger->info("Finishing task {$className}");
        } catch (\Exception $e) {
            $this->logger->info("Error occurred when run task {$className}");
            $this->logger->error($e->getMessage());
            return false;
        }

        return true;
    }

    private function cleanUp(): void
    {
        gc_collect_cycles();
    }
}
