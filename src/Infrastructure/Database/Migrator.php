<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

final class Migrator
{
    public function __construct(private ConnectionInterface $connection) {}

    public function run(string $directory): void
    {
        $this->ensureTable();

        $applied = $this->applied();
        foreach ($this->files($directory) as $file) {
            $name = basename($file, '.php');
            if (in_array($name, $applied, true)) {
                continue;
            }

            $this->migration($file, $name)->up($this->connection);
            $this->connection->execute(
                'INSERT INTO migrations (name) VALUES (:name)',
                ['name' => $name],
            );
        }
    }

    public function rollback(string $directory): ?string
    {
        $this->ensureTable();
        $row = $this->connection->fetch('SELECT name FROM migrations ORDER BY id DESC LIMIT 1');
        if ($row === null) {
            return null;
        }

        $name = (string) $row['name'];
        $file = $directory . '/' . $name . '.php';
        $this->migration($file, $name)->down($this->connection);
        $this->connection->execute(
            'DELETE FROM migrations WHERE name = :name',
            ['name' => $name],
        );

        return $name;
    }

    private function ensureTable(): void
    {
        $this->connection->execute(
            'CREATE TABLE IF NOT EXISTS migrations (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY migrations_name (name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
        );
    }

    /** @return list<string> */
    private function applied(): array
    {
        $applied = [];
        foreach ($this->connection->fetchAll('SELECT name FROM migrations') as $row) {
            $applied[] = (string) $row['name'];
        }

        return $applied;
    }

    /** @return list<string> */
    private function files(string $directory): array
    {
        $files = glob($directory . '/*.php');
        if ($files === false) {
            return [];
        }

        sort($files);

        return $files;
    }

    private function migration(string $file, string $name): MigrationInterface
    {
        if (!is_file($file)) {
            throw new \RuntimeException('Файл миграции не найден: ' . $name);
        }

        $migration = require $file;
        if (!$migration instanceof MigrationInterface) {
            throw new \RuntimeException('Файл миграции должен возвращать ' . MigrationInterface::class . ': ' . $name);
        }

        return $migration;
    }
}
