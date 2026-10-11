<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\ArticleSort;
use App\Domain\Page;
use App\Domain\Entity\Article;
use App\Domain\Entity\Category;
use App\Domain\Entity\File;
use App\Domain\Repository\ArticleRepository;
use App\Domain\Repository\CategoryRepository;
use App\Domain\Repository\FileRepository;

final class ArticleService
{
    public const PER_PAGE = 6;

    public const RELATED_LIMIT = 3;

    public function __construct(
        private ArticleRepository $articles,
        private CategoryRepository $categories,
        private FileRepository $files,
    ) {}

    public function create(Article $article): Article
    {
        return $this->articles->add($article);
    }

    /** @return list<Article> */
    public function all(): array
    {
        return $this->articles->all();
    }

    /**
     * @param list<int> $categoryIds
     *
     * @return array{groups: array<int, list<Article>>, files: array<int, File>}
     */
    public function latestPerCategory(int $limit, array $categoryIds): array
    {
        $groups = $this->articles->latestPerCategory(max(1, $limit), $categoryIds);
        $articles = [];
        foreach ($groups as $group) {
            foreach ($group as $article) {
                $articles[] = $article;
            }
        }

        return [
            'groups' => $groups,
            'files' => $this->filesFor($articles),
        ];
    }

    public function pageByCategory(int $categoryId, ArticleSort $sort, int $page, int $perPage = self::PER_PAGE): Page
    {
        $perPage = max(1, $perPage);
        $total = $this->articles->countByCategory($categoryId);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $pages);

        return new Page(
            $this->articles->listByCategory($categoryId, $sort, $perPage, ($page - 1) * $perPage),
            $total,
            $page,
            $perPage,
        );
    }

    /**
     * @return array{article: Article, categories: list<Category>, related: list<Article>, files: array<int, File>}|null
     */
    public function details(int $id): ?array
    {
        $article = $this->articles->find($id);
        if ($article === null) {
            return null;
        }

        $related = $this->articles->related($id, max(1, self::RELATED_LIMIT));

        return [
            'article' => $article,
            'categories' => $this->categories->findByIds($article->categoryIds),
            'related' => $related,
            'files' => $this->filesFor([$article, ...$related]),
        ];
    }

    public function views(int $id): ?int
    {
        return $this->articles->find($id)?->views;
    }

    public function registerView(int $id): ?int
    {
        if ($this->articles->incrementViews($id) === 0) {
            return null;
        }

        return $this->articles->find($id)?->views;
    }

    /**
     * @param list<Article> $articles
     *
     * @return array<int, File>
     */
    public function filesFor(array $articles): array
    {
        $ids = [];
        foreach ($articles as $article) {
            if ($article->fileId !== null) {
                $ids[] = $article->fileId;
            }
        }

        return $this->files->findByIds($ids);
    }
}
