<?php

declare(strict_types=1);

use App\Infrastructure\Database\ConnectionInterface;
use App\Infrastructure\Database\MigrationInterface;

return new class implements MigrationInterface {
    public function up(ConnectionInterface $connection): void
    {
        $connection->execute(
            'CREATE TABLE session_token_links (
                session_id INT UNSIGNED NOT NULL,
                token CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
                PRIMARY KEY (token),
                CONSTRAINT session_token_links_session_fk FOREIGN KEY (session_id) REFERENCES sessions (id) ON DELETE CASCADE,
                CONSTRAINT session_token_links_token_fk FOREIGN KEY (token) REFERENCES tokens (token) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
        );
    }

    public function down(ConnectionInterface $connection): void
    {
        $connection->execute('DROP TABLE session_token_links');
    }
};
