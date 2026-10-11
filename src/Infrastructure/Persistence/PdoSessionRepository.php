<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\Session;
use App\Domain\Repository\SessionRepository;
use App\Infrastructure\Database\ConnectionInterface;
use DateTimeImmutable;

final class PdoSessionRepository implements SessionRepository
{
    public function __construct(private ConnectionInterface $connection) {}

    public function find(int $id): ?Session
    {
        $row = $this->connection->fetch(
            'SELECT id, expires_at FROM sessions WHERE id = :id',
            ['id' => $id],
        );
        if ($row === null) {
            return null;
        }

        return $this->map($row);
    }

    public function add(DateTimeImmutable $expiresAt): Session
    {
        $this->connection->execute(
            'INSERT INTO sessions (expires_at) VALUES (:expires_at)',
            ['expires_at' => $expiresAt->format('Y-m-d H:i:s')],
        );

        return new Session($this->connection->lastInsertId(), $expiresAt);
    }

    public function addToken(int $sessionId, string $token): void
    {
        $this->connection->execute(
            'INSERT INTO session_token_links (session_id, token) VALUES (:session_id, :token)',
            [
                'session_id' => $sessionId,
                'token' => $token,
            ],
        );
    }

    public function hasToken(int $sessionId, string $token): bool
    {
        return $this->connection->fetch(
            'SELECT session_id FROM session_token_links WHERE session_id = :session_id AND token = :token',
            [
                'session_id' => $sessionId,
                'token' => $token,
            ],
        ) !== null;
    }

    /** @param array<string, mixed> $row */
    private function map(array $row): Session
    {
        return new Session(
            (int) $row['id'],
            new DateTimeImmutable((string) $row['expires_at']),
        );
    }
}
