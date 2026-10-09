<?php

declare(strict_types=1);

use App\Console\Application;
use App\Http\Kernel;
use App\Http\Router;
use App\Http\ViewInterface;
use App\Infrastructure\Container;
use App\Infrastructure\View\FileRenderer;

return [
    FileRenderer::class => static fn (): FileRenderer => new FileRenderer(BASE_PATH . '/resources/views'),
    ViewInterface::class => FileRenderer::class,
    Kernel::class => static fn (Container $container): Kernel => new Kernel(
        $container->get(Router::class),
    ),
    Router::class => static fn (Container $container): Router => new Router(
        require BASE_PATH . '/routes/web.php',
        static fn (string $class): object => $container->get($class),
    ),
    Application::class => static fn (): Application => new Application([]),
];
