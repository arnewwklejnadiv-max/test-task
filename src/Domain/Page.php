<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Entity\Article;

final readonly class Page
{
    /** @param list<Article> $items */
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $perPage,
    ) {}

    public function pages(): int
    {
        return max(1, (int) ceil($this->total / max(1, $this->perPage)));
    }
}
