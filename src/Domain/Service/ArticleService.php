<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Article;
use App\Domain\Repository\ArticleRepository;
use App\Domain\Validation\ArticleValidator;

final class ArticleService
{
    public function __construct(
        private ArticleRepository $articles,
        private ArticleValidator $validator,
    ) {}

    public function create(Article $article): Article
    {
        $this->validator->assertValid($article);

        return $this->articles->add($article);
    }

    /** @return list<Article> */
    public function all(): array
    {
        return $this->articles->all();
    }
}
