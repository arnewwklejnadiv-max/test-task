<?php

declare(strict_types=1);

namespace App\Http;

use App\Http\Exception\MethodNotAllowedException;
use App\Http\Exception\NotFoundException;

final class Router
{
    /**
     * @param list<array{0: string, 1: string, 2: array{0: class-string, 1: string}}> $routes
     */
    private array $routes;

    /**
     * @param list<array{0: string, 1: string, 2: array{0: class-string, 1: string}}>|mixed $routes
     * @param callable(class-string): object $controllers
     */
    public function __construct(
        mixed $routes,
        private $controllers,
    ) {
        if (!is_array($routes)) {
            throw new \RuntimeException('routes/web.php должен возвращать список маршрутов');
        }

        $this->routes = $routes;
    }

    public function dispatch(Request $request): Response
    {
        $allowed = [];
        foreach ($this->routes as [$method, $path, $handler]) {
            if ($path !== $request->path()) {
                continue;
            }
            $allowed[] = $method;
            if ($method === $request->method()) {
                return $this->invoke($handler, $request);
            }
        }

        if ($allowed !== []) {
            throw new MethodNotAllowedException($allowed);
        }

        throw new NotFoundException();
    }

    /** @param array{0: class-string, 1: string} $handler */
    private function invoke(array $handler, Request $request): Response
    {
        [$class, $action] = $handler;
        $controller = ($this->controllers)($class);
        if (!method_exists($controller, $action)) {
            throw new \RuntimeException('Действие контроллера не найдено');
        }

        $response = $controller->{$action}($request);
        if (!$response instanceof Response) {
            throw new \RuntimeException('Действие контроллера должно вернуть ответ');
        }

        return $response;
    }
}
