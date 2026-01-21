<?php

namespace PhpFramework;

use GuzzleHttp\Psr7\ServerRequest;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use PhpFramework\Console\ArgsInputs;
use PhpFramework\Console\Command;
use PhpFramework\Console\ConsoleException;
use PhpFramework\Console\ConsoleInterface;
use PhpFramework\Database\Database;
use PhpFramework\Database\DatabaseInterface;
use PhpFramework\DI\Container;

class Application implements ConsoleInterface
{
    public static Application|null $app = null;

    private string $basePath;

    private Container $container;
    private ApplicationStrategy $strategy;

    private Router $router;

    public array $commands = [
        \PhpFramework\Console\Commands\ListCommand::class,
        \PhpFramework\Console\Commands\TestsCommand::class
    ] {
        get {
            return $this->commands;
        }
    }

    public function __construct()
    {
        $this->container = new Container([
            DatabaseInterface::class => Database::class
        ]);

        $this->strategy = new ApplicationStrategy();
        $this->strategy->setContainer($this->container);

        $this->router = new Router;
        $this->router->setStrategy($this->strategy);
    }

    /**
     * Include router requests
     *
     * @param callable $callback
     * @return $this
     */
    public function withRouting(callable $callback): self
    {
        $callback($this->router);
        return $this;
    }

    /**
     * Include console commands
     *
     * @param array $commands
     * @return $this
     */
    public function withCommands(array $commands): self
    {
        $this->commands = array_merge($this->commands, $commands);
        return $this;
    }

    /**
     * Set the base application path
     *
     * @param string $basePath
     * @return $this
     */
    public function setBasePath(string $basePath): self
    {
        $this->basePath = $basePath;
        return $this;
    }

    /**
     * Get the base application path
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Set class to dependency injector
     *
     * @param string $id
     * @param string $value
     * @return $this
     */
    public function setContainerDependency(string $id, string $value): self
    {
        $this->container->set($id, $value);
        return $this;
    }

    /**
     * Run the application with router
     *
     * @return void
     */
    public function run(): void
    {
        $request = ServerRequest::fromGlobals();
        $response = $this->router->dispatch($request);

        new SapiEmitter()->emit($response);
    }

    /**
     * Handles the console command
     *
     * @param ArgsInputs $argsInputs
     * @return void
     */
    public function handleCommand(ArgsInputs $argsInputs): void
    {
        try {
            $inputs = $argsInputs->parse();
            $parsedCommand = null;
            foreach ($this->commands as $commandClass) {
                /** @var Command $command */
                $command = new \ReflectionClass($commandClass)->newInstance();
                if ($command->name === $inputs[0]) {
                    $parsedCommand = $command;
                }
            }

            if (!$parsedCommand) {
                throw new ConsoleException("There is no command with name '{$inputs[0]}' found");
            }

            $parsedCommand->run(array_splice($inputs, 1));
        } catch (\Throwable $e) {
            echo "An exception occurred: " . $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * Make the singleton Application object
     *
     * @return Application
     */
    public static function instance(): Application
    {
        if (static::$app === null) {
            static::$app = new Application();
        }

        return static::$app;
    }
}
