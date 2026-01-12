<?php

namespace PhpFramework\Controller;

use PhpFramework\View\View;
use Psr\Http\Message\ResponseInterface;

class AbstractController
{
    public function render(string $viewPath, array $data = []): ResponseInterface
    {
        return View::render($viewPath, $data);
    }
}
