<?php

declare(strict_types=1);

use App\Infrastructure\Database\ConnectionInterface;
use App\Infrastructure\Database\MigrationInterface;

return new class implements MigrationInterface {
    public function up(ConnectionInterface $connection): void
    {
        $connection->execute(
            'CREATE TABLE articles (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                image VARCHAR(255) NOT NULL,
                name VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                `text` MEDIUMTEXT NOT NULL,
                published_at DATETIME NULL,
                views INT UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
        );
    }

    public function down(ConnectionInterface $connection): void
    {
        $connection->execute('DROP TABLE articles');
    }
};
