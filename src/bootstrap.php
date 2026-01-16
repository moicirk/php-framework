<?php

declare(strict_types=1);
ini_set('display_errors', 1);

use App\Controllers\HomeController;
use App\Controllers\ProductsController;
use PhpFramework\Databases\Database;
use PhpFramework\Databases\DatabaseInterface;
use PhpFramework\DI\Container;
use GuzzleHttp\Psr7\ServerRequest;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;

require_once dirname(__DIR__) . '/vendor/autoload.php';

define('APP_ROOT', dirname(__DIR__));

$container = new Container([
    DatabaseInterface::class => Database::class
]);

$strategy = new ApplicationStrategy();
$strategy->setContainer($container);

$router = new Router;
$router->setStrategy($strategy);

$router->get('/', [HomeController::class, 'index']);
$router->get('/products', [ProductsController::class, 'index']);
$router->get('/product/{id:number}', [ProductsController::class, 'show']);

$request = ServerRequest::fromGlobals();
$response = $router->dispatch($request);

new SapiEmitter()->emit($response);
