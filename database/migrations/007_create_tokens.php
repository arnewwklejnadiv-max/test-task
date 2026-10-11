<?php

declare(strict_types=1);

use App\Infrastructure\Database\ConnectionInterface;
use App\Infrastructure\Database\MigrationInterface;

return new class implements MigrationInterface {
    public function up(ConnectionInterface $connection): void
    {
        $connection->execute(
            'CREATE TABLE tokens (
                token CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
                expires_at DATETIME NOT NULL,
                used_at DATETIME NULL,
                PRIMARY KEY (token)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
        );
    }

    public function down(ConnectionInterface $connection): void
    {
        $connection->execute('DROP TABLE tokens');
    }
};
