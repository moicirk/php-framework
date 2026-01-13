<?php

namespace App\Controllers;

use App\Entities\Product;
use GuzzleHttp\Psr7\ServerRequest;
use PhpFramework\Controller\AbstractController;
use PhpFramework\Database\Database;
use Psr\Http\Message\ResponseInterface;

class ProductsController extends AbstractController
{
    public function index(): ResponseInterface
    {
        $doctrine = new Database();
        $products = $doctrine
            ->getRepository(Product::class)
            ->findAll();

        return $this->render('products/index', [
            'products' => $products
        ]);
    }

    public function show(ServerRequest $request): ResponseInterface
    {
        return $this->render('products/show', [
            'id' => $request->getAttribute('id')
        ]);
    }
}
