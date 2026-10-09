<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Article;

interface ArticleRepository
{
    public function add(Article $article): Article;

    public function find(int $id): ?Article;

    /** @return list<Article> */
    public function all(): array;

    public function incrementViews(int $id): void;
}
