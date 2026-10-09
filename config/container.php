<?php

declare(strict_types=1);

use App\Console\Application;
use App\Console\Commands\MigrateCommand;
use App\Http\Kernel;
use App\Http\Router;
use App\Http\ViewInterface;
use App\Infrastructure\Config\Env;
use App\Infrastructure\Container;
use App\Infrastructure\Database\ConnectionInterface;
use App\Infrastructure\Database\Migrator;
use App\Infrastructure\Database\PdoConnection;
use App\Infrastructure\View\FileRenderer;

return [
    ConnectionInterface::class => static fn (): ConnectionInterface => new PdoConnection(
        Env::string('DB_HOST'),
        Env::int('DB_PORT'),
        Env::string('DB_NAME'),
        Env::string('DB_USER'),
        Env::string('DB_PASSWORD'),
    ),
    Migrator::class => static fn (Container $container): Migrator => new Migrator(
        $container->get(ConnectionInterface::class),
    ),
    FileRenderer::class => static fn (): FileRenderer => new FileRenderer(BASE_PATH . '/resources/views'),
    ViewInterface::class => FileRenderer::class,
    Kernel::class => static fn (Container $container): Kernel => new Kernel(
        $container->get(Router::class),
    ),
    Router::class => static fn (Container $container): Router => new Router(
        require BASE_PATH . '/routes/web.php',
        static fn (string $class): object => $container->get($class),
    ),
    Application::class => static fn (Container $container): Application => new Application([
        $container->get(MigrateCommand::class),
    ]),
];
