<?php

declare(strict_types=1);

use App\Console\Application;
use App\Console\Commands\MigrateCommand;
use App\Console\Commands\SeedCommand;
use App\Domain\Repository\ArticleRepository;
use App\Domain\Repository\CategoryRepository;
use App\Domain\Repository\FileRepository;
use App\Http\Kernel;
use App\Http\Router;
use App\Http\ViewInterface;
use App\Infrastructure\Config\Env;
use App\Infrastructure\Container;
use App\Infrastructure\Database\ConnectionInterface;
use App\Infrastructure\Database\Migrator;
use App\Infrastructure\Database\PdoConnection;
use App\Infrastructure\Persistence\PdoArticleRepository;
use App\Infrastructure\Persistence\PdoCategoryRepository;
use App\Infrastructure\Persistence\PdoFileRepository;
use App\Infrastructure\View\SmartyRenderer;

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
    ArticleRepository::class => PdoArticleRepository::class,
    CategoryRepository::class => PdoCategoryRepository::class,
    FileRepository::class => PdoFileRepository::class,
    SmartyRenderer::class => static fn (): SmartyRenderer => new SmartyRenderer(
        BASE_PATH . '/resources/views',
        sys_get_temp_dir() . '/smarty',
        !Env::bool('APP_DEBUG'),
    ),
    ViewInterface::class => SmartyRenderer::class,
    Kernel::class => static fn (Container $container): Kernel => new Kernel(
        $container->get(Router::class),
    ),
    Router::class => static fn (Container $container): Router => new Router(
        require BASE_PATH . '/routes/web.php',
        static fn (string $class): object => $container->get($class),
    ),
    Application::class => static fn (Container $container): Application => new Application([
        $container->get(MigrateCommand::class),
        $container->get(SeedCommand::class),
    ]),
];
