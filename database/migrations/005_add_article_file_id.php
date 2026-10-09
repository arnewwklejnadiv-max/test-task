<?php

declare(strict_types=1);

use App\Infrastructure\Database\ConnectionInterface;
use App\Infrastructure\Database\MigrationInterface;

return new class implements MigrationInterface {
    public function up(ConnectionInterface $connection): void
    {
        $connection->execute('ALTER TABLE articles ADD file_id INT UNSIGNED NULL AFTER id');
        $connection->execute('ALTER TABLE articles DROP COLUMN image');
        $connection->execute(
            'ALTER TABLE articles ADD CONSTRAINT articles_file_fk FOREIGN KEY (file_id) REFERENCES files (id) ON DELETE SET NULL',
        );
    }

    public function down(ConnectionInterface $connection): void
    {
        $connection->execute('ALTER TABLE articles DROP FOREIGN KEY articles_file_fk');
        $connection->execute('ALTER TABLE articles DROP COLUMN file_id');
        $connection->execute('ALTER TABLE articles ADD image VARCHAR(255) NOT NULL AFTER id');
    }
};
