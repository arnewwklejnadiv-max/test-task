<?php

declare(strict_types=1);

use App\Infrastructure\Database\ConnectionInterface;
use App\Infrastructure\Database\MigrationInterface;

return new class implements MigrationInterface {
    public function up(ConnectionInterface $connection): void
    {
        $connection->execute(
            'CREATE TABLE sessions (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                expires_at DATETIME NOT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
        );
    }

    public function down(ConnectionInterface $connection): void
    {
        $connection->execute('DROP TABLE sessions');
    }
};
