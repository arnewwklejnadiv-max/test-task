<?php

declare(strict_types=1);

namespace App\Http;

final class Kernel
{
    public function __construct(private Router $router) {}

    public function handle(Request $request): Response
    {
        return $this->router->dispatch($request);
    }
}
