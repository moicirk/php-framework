<?php

namespace PhpFramework\Views;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

class View
{
    public static string|null $layout = null;
    public static array $sections = [];

    public static function extends(string $layout): void
    {
        self::$layout = $layout;
    }

    public static function section(): void
    {
        ob_start();
    }

    public static function endSection(string $name): void
    {
        self::$sections[$name] = ob_get_clean();
    }

    public static function render(string $viewFile, array $data = []): ResponseInterface
    {
        self::$layout = null;
        self::$sections = [];

        return new HtmlResponse(
            self::renderView($viewFile, $data)
        );
    }

    private static function renderView(string $viewFile, array $data): string
    {
        extract($data);

        ob_start();
        include self::getViewPath() . "/{$viewFile}.php";
        $content = ob_get_clean();

        if (self::$layout) {
            ob_start();
            include self::getViewPath() . "/layouts/" . self::$layout . ".php";

            return ob_get_clean();
        }

        return $content;
    }

    private static function getViewPath(): string
    {
        return APP_ROOT . "/views";
    }
}
