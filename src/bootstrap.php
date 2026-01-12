<?php

declare(strict_types=1);
ini_set('display_errors', 1);

use App\Controllers\HomeController;
use App\Controllers\ProductsController;
use GuzzleHttp\Psr7\ServerRequest;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Router;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$request = ServerRequest::fromGlobals();

$router = new Router();
$router->get('/', [HomeController::class, 'index']);
$router->get('/products', [ProductsController::class, 'index']);
$router->get('/product/{id:number}', [ProductsController::class, 'show']);

$response = $router->dispatch($request);

new SapiEmitter()->emit($response);
