<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeImmutable;

final readonly class Session
{
    public function __construct(
        public ?int $id,
        public DateTimeImmutable $expiresAt,
    ) {}
}
