<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\ArticleSort;
use App\Domain\Entity\Article;

interface ArticleRepository
{
    public function add(Article $article): Article;

    public function find(int $id): ?Article;

    /** @return list<Article> */
    public function all(): array;

    /**
     * @param list<int> $categoryIds
     *
     * @return array<int, list<Article>>
     */
    public function latestPerCategory(int $limit, array $categoryIds): array;

    public function countByCategory(int $categoryId): int;

    /** @return list<Article> */
    public function listByCategory(int $categoryId, ArticleSort $sort, int $limit, int $offset): array;

    /** @return list<Article> */
    public function related(int $id, int $limit): array;

    public function incrementViews(int $id): int;
}
