<?php

namespace App\Controllers;

use GuzzleHttp\Psr7\ServerRequest;
use PhpFramework\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;

class ProductsController extends AbstractController
{
    public function index(): ResponseInterface
    {
        return $this->render('products/index');
    }

    public function show(ServerRequest $request): ResponseInterface
    {
        return $this->render('products/show', [
            'id' => $request->getAttribute('id')
        ]);
    }
}
