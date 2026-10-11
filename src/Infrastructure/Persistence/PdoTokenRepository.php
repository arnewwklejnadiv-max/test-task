<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\Token;
use App\Domain\Repository\TokenRepository;
use App\Infrastructure\Database\ConnectionInterface;
use DateTimeImmutable;

final class PdoTokenRepository implements TokenRepository
{
    public function __construct(private ConnectionInterface $connection) {}

    public function add(string $token, DateTimeImmutable $expiresAt): Token
    {
        $this->connection->execute(
            'INSERT INTO tokens (token, expires_at) VALUES (:token, :expires_at)',
            [
                'token' => $token,
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            ],
        );

        return new Token($token, $expiresAt, null);
    }

    public function find(string $token): ?Token
    {
        $row = $this->connection->fetch(
            'SELECT token, expires_at, used_at FROM tokens WHERE token = :token',
            ['token' => $token],
        );
        if ($row === null) {
            return null;
        }

        return $this->map($row);
    }

    public function consume(string $token, DateTimeImmutable $usedAt): bool
    {
        return $this->connection->execute(
            'UPDATE tokens
             SET used_at = :used_at
             WHERE token = :token
               AND used_at IS NULL
               AND expires_at > :now',
            [
                'used_at' => $usedAt->format('Y-m-d H:i:s'),
                'now' => $usedAt->format('Y-m-d H:i:s'),
                'token' => $token,
            ],
        ) === 1;
    }

    /** @param array<string, mixed> $row */
    private function map(array $row): Token
    {
        $usedAt = $row['used_at'] ?? null;

        return new Token(
            (string) $row['token'],
            new DateTimeImmutable((string) $row['expires_at']),
            $usedAt === null || $usedAt === '' ? null : new DateTimeImmutable((string) $usedAt),
        );
    }
}
