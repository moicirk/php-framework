<?php
declare(strict_types=1);
ini_set('display_errors', 1);

use App\Controllers\HomeController;
use App\Controllers\ProductsController;
use PhpFramework\Application;
use League\Route\Router;

require_once dirname(__DIR__) . '/vendor/autoload.php';

Application::instance()
    ->setBasePath(dirname(__DIR__))
    ->setContainerDependency(HomeController::class, HomeController::class)
    ->setContainerDependency(ProductsController::class, ProductsController::class)
    ->withRouting(function (Router $router) {
        $router->get('/', [HomeController::class, 'index']);
        $router->get('/products', [ProductsController::class, 'index']);
        $router->get('/product/{id:number}', [ProductsController::class, 'show']);
    })
    ->run();
