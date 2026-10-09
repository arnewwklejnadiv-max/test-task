<?php

declare(strict_types=1);

namespace App\Domain\Entity;

final readonly class File
{
    public function __construct(
        public ?int $id,
        public string $path,
        public string $name,
    ) {}
}
