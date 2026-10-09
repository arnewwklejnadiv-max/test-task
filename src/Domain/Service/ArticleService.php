<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Article;
use App\Domain\Repository\ArticleRepository;

final class ArticleService
{
    public function __construct(private ArticleRepository $articles) {}

    public function create(Article $article): Article
    {
        return $this->articles->add($article);
    }

    /** @return list<Article> */
    public function all(): array
    {
        return $this->articles->all();
    }
}
