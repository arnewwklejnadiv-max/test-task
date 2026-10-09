<?php

declare(strict_types=1);

namespace App;

use App\Infrastructure\Config\Env;
use App\Infrastructure\Container;

final class Bootstrap
{
    private static ?Container $container = null;

    public static function container(): Container
    {
        if (self::$container instanceof Container) {
            return self::$container;
        }

        if (!defined('BASE_PATH')) {
            define('BASE_PATH', dirname(__DIR__));
        }

        Env::load(BASE_PATH . '/.env');

        /** @var array<string, callable(Container): object|class-string> $definitions */
        $definitions = require BASE_PATH . '/config/container.php';
        self::$container = new Container($definitions);

        return self::$container;
    }
}
