<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeImmutable;

final readonly class Article
{
    /** @param list<int> $categoryIds */
    public function __construct(
        public ?int $id,
        public string $image,
        public string $name,
        public string $description,
        public string $text,
        public ?DateTimeImmutable $publishedAt,
        public int $views,
        public array $categoryIds,
    ) {}
}
