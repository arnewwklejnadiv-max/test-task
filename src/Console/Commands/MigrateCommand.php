<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\CommandInterface;
use App\Infrastructure\Database\Migrator;

final class MigrateCommand implements CommandInterface
{
    public function __construct(private Migrator $migrator) {}

    public function name(): string
    {
        return 'database:migrate';
    }

    public function description(): string
    {
        return 'Применить миграции базы данных';
    }

    /** @param list<string> $arguments */
    public function execute(array $arguments): int
    {
        $this->migrator->run(dirname(__DIR__, 3) . '/database/migrations');
        fwrite(STDOUT, "Миграции применены.\n");

        return 0;
    }
}
