<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Token;
use DateTimeImmutable;

interface TokenRepository
{
    public function add(string $token, DateTimeImmutable $expiresAt): Token;

    public function find(string $token): ?Token;

    public function consume(string $token, DateTimeImmutable $usedAt): bool;
}
