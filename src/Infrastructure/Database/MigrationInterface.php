<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

interface MigrationInterface
{
    public function up(ConnectionInterface $connection): void;

    public function down(ConnectionInterface $connection): void;
}
