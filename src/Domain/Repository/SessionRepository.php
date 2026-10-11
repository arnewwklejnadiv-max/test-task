<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Session;
use DateTimeImmutable;

interface SessionRepository
{
    public function find(int $id): ?Session;

    public function add(DateTimeImmutable $expiresAt): Session;

    public function addToken(int $sessionId, string $token): void;

    public function hasToken(int $sessionId, string $token): bool;
}
