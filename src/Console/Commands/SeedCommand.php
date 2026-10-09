<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\CommandInterface;
use Database\Seed\DatabaseSeeder;
use RuntimeException;

final class SeedCommand implements CommandInterface
{
    public function __construct(private DatabaseSeeder $seeder) {}

    public function name(): string
    {
        return 'database:seed';
    }

    public function description(): string
    {
        return 'Заполнить базу примерами категорий и статей';
    }

    /** @param list<string> $arguments */
    public function execute(array $arguments): int
    {
        try {
            $this->seeder->run();
        } catch (RuntimeException $exception) {
            fwrite(STDERR, $exception->getMessage() . "\n");

            return 1;
        }

        fwrite(STDOUT, "База заполнена.\n");

        return 0;
    }
}
