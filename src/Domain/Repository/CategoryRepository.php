<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Category;

interface CategoryRepository
{
    public function add(Category $category): Category;

    public function find(int $id): ?Category;

    /** @return list<Category> */
    public function all(): array;

    /** @return list<Category> */
    public function withArticles(int $limit, int $offset): array;

    /**
     * @param list<int> $ids
     *
     * @return list<Category>
     */
    public function findByIds(array $ids): array;
}
