<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeImmutable;

final readonly class Token
{
    public function __construct(
        public string $token,
        public DateTimeImmutable $expiresAt,
        public ?DateTimeImmutable $usedAt,
    ) {}
}
