<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

interface ConnectionInterface
{
    /** @param array<string, mixed> $params */
    public function execute(string $sql, array $params = []): int;

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>|null
     */
    public function fetch(string $sql, array $params = []): ?array;

    /**
     * @param array<string, mixed> $params
     *
     * @return list<array<string, mixed>>
     */
    public function fetchAll(string $sql, array $params = []): array;

    public function lastInsertId(): int;

    public function transaction(callable $callback): mixed;
}
