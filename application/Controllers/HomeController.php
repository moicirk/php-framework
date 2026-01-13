<?php

namespace App\Controllers;

use PhpFramework\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;

class HomeController extends AbstractController
{
    public function index(): ResponseInterface
    {
        return $this->render('home/index', [
            'name' => 'Igor Mur'
        ]);
    }
}
