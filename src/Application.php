<?php

namespace PhpFramework;

use GuzzleHttp\Psr7\ServerRequest;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use PhpFramework\Database\Database;
use PhpFramework\Database\DatabaseInterface;
use PhpFramework\DI\Container;

class Application
{
    public static Application|null $app = null;

    private string $basePath;

    private Container $container;
    private ApplicationStrategy $strategy;

    private Router $router;

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

    public function withRouting(callable $callback): self
    {
        $callback($this->router);
        return $this;
    }

    public function setBasePath(string $basePath): self
    {
        $this->basePath = $basePath;
        return $this;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function setContainerDependency(string $id, string $value): self
    {
        $this->container->set($id, $value);
        return $this;
    }

    public function run(): void
    {
        $request = ServerRequest::fromGlobals();
        $response = $this->router->dispatch($request);

        new SapiEmitter()->emit($response);
    }

    public static function instance(): Application
    {
        if (static::$app === null) {
            static::$app = new Application();
        }

        return static::$app;
    }
}
