<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use PDO;
use PDOStatement;
use Throwable;

final class PdoConnection implements ConnectionInterface
{
    private ?PDO $pdo = null;

    private int $transactionLevel = 0;

    public function __construct(
        private string $host,
        private int $port,
        private string $name,
        private string $user,
        private string $password,
    ) {}

    private function pdo(): PDO
    {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }

        return $this->pdo = new PDO(
            sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $this->host, $this->port, $this->name),
            $this->user,
            $this->password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        );
    }

    public function execute(string $sql, array $params = []): int
    {
        return $this->statement($sql, $params)->rowCount();
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $row = $this->statement($sql, $params)->fetch();

        return is_array($row) ? $row : null;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->statement($sql, $params)->fetchAll();
    }

    public function lastInsertId(): int
    {
        return (int) $this->pdo()->lastInsertId();
    }

    public function transaction(callable $callback): mixed
    {
        $this->begin();

        try {
            $result = $callback($this);
            $this->commit();

            return $result;
        } catch (Throwable $exception) {
            $this->rollBack();

            throw $exception;
        }
    }

    private function begin(): void
    {
        if ($this->transactionLevel === 0) {
            $this->pdo()->beginTransaction();
        } else {
            $this->pdo()->exec('SAVEPOINT transaction_' . $this->transactionLevel);
        }

        ++$this->transactionLevel;
    }

    private function commit(): void
    {
        --$this->transactionLevel;
        if ($this->transactionLevel === 0) {
            $this->pdo()->commit();

            return;
        }

        $this->pdo()->exec('RELEASE SAVEPOINT transaction_' . $this->transactionLevel);
    }

    private function rollBack(): void
    {
        --$this->transactionLevel;
        if ($this->transactionLevel === 0) {
            if ($this->pdo()->inTransaction()) {
                $this->pdo()->rollBack();
            }

            return;
        }

        $this->pdo()->exec('ROLLBACK TO SAVEPOINT transaction_' . $this->transactionLevel);
    }

    /** @param array<int|string, mixed> $params */
    private function statement(string $sql, array $params): PDOStatement
    {
        $statement = $this->pdo()->prepare($sql);
        $statement->execute($params);

        return $statement;
    }
}
