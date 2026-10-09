<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\ArticleSort;
use App\Domain\Entity\Article;
use App\Domain\Repository\ArticleRepository;
use App\Infrastructure\Database\ConnectionInterface;
use DateTimeImmutable;

final class PdoArticleRepository implements ArticleRepository
{
    public function __construct(private ConnectionInterface $connection) {}

    public function add(Article $article): Article
    {
        $created = $this->connection->transaction(function () use ($article): Article {
            $this->connection->execute(
                'INSERT INTO articles (file_id, name, description, `text`, published_at) VALUES (:file_id, :name, :description, :text, :published_at)',
                [
                    'file_id' => $article->fileId,
                    'name' => $article->name,
                    'description' => $article->description,
                    'text' => $article->text,
                    'published_at' => $article->publishedAt?->format('Y-m-d H:i:s'),
                ],
            );
            $id = $this->connection->lastInsertId();
            foreach ($article->categoryIds as $categoryId) {
                $this->connection->execute(
                    'INSERT INTO article_category (article_id, category_id) VALUES (:article_id, :category_id)',
                    [
                        'article_id' => $id,
                        'category_id' => $categoryId,
                    ],
                );
            }

            return new Article(
                $id,
                $article->fileId,
                $article->name,
                $article->description,
                $article->text,
                $article->publishedAt,
                0,
                $article->categoryIds,
            );
        });
        if (!$created instanceof Article) {
            throw new \RuntimeException('Статья не создана');
        }

        return $created;
    }

    public function find(int $id): ?Article
    {
        $row = $this->connection->fetch(
            'SELECT id, file_id, name, description, `text`, published_at, views FROM articles WHERE id = :id',
            ['id' => $id],
        );
        if ($row === null) {
            return null;
        }

        return $this->hydrate([$row])[0];
    }

    public function all(): array
    {
        return $this->hydrate($this->connection->fetchAll(
            'SELECT id, file_id, name, description, `text`, published_at, views FROM articles ORDER BY id',
        ));
    }

    public function latestPerCategory(int $limit, array $categoryIds): array
    {
        $categoryIds = array_values(array_unique(array_filter(
            $categoryIds,
            static fn (int $id): bool => $id > 0,
        )));
        if ($categoryIds === []) {
            return [];
        }

        $grouped = [];
        foreach ($categoryIds as $categoryId) {
            $rows = $this->connection->fetchAll(
                'SELECT a.id, a.file_id, a.name, a.description, a.`text`, a.published_at, a.views
                 FROM articles a
                 INNER JOIN article_category ac ON ac.article_id = a.id
                 WHERE ac.category_id = :id
                 ORDER BY a.published_at DESC, a.id DESC
                 LIMIT ' . $limit,
                ['id' => $categoryId],
            );
            if ($rows === []) {
                continue;
            }
            $grouped[$categoryId] = $this->hydrate($rows);
        }

        return $grouped;
    }

    public function countByCategory(int $categoryId): int
    {
        $row = $this->connection->fetch(
            'SELECT COUNT(*) AS total FROM article_category WHERE category_id = :id',
            ['id' => $categoryId],
        );

        return (int) ($row['total'] ?? 0);
    }

    public function listByCategory(int $categoryId, ArticleSort $sort, int $limit, int $offset): array
    {
        return $this->hydrate($this->connection->fetchAll(
            'SELECT a.id, a.file_id, a.name, a.description, a.`text`, a.published_at, a.views
             FROM articles a
             INNER JOIN article_category ac ON ac.article_id = a.id
             WHERE ac.category_id = :id
             ORDER BY ' . $this->orderBy($sort) . '
             LIMIT ' . $limit . ' OFFSET ' . $offset,
            ['id' => $categoryId],
        ));
    }

    public function related(int $id, int $limit): array
    {
        return $this->hydrate($this->connection->fetchAll(
            'SELECT a.id, a.file_id, a.name, a.description, a.`text`, a.published_at, a.views
             FROM articles a
             INNER JOIN article_category ac ON ac.article_id = a.id
             WHERE ac.category_id IN (
                 SELECT category_id FROM article_category WHERE article_id = :id
             )
               AND a.id <> :current
             GROUP BY a.id
             ORDER BY COUNT(*) DESC, a.published_at DESC, a.id DESC
             LIMIT ' . $limit,
            ['id' => $id, 'current' => $id],
        ));
    }

    public function incrementViews(int $id): int
    {
        return $this->connection->execute(
            'UPDATE articles SET views = views + 1 WHERE id = :id',
            ['id' => $id],
        );
    }

    /**
     * @param list<array<string, mixed>> $rows
     *
     * @return list<Article>
     */
    private function hydrate(array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        $ids = array_values(array_unique(array_map(static fn (array $row): int => (int) $row['id'], $rows)));
        $links = $this->connection->fetchAll(
            'SELECT article_id, category_id FROM article_category WHERE article_id IN ('
            . implode(', ', array_fill(0, count($ids), '?'))
            . ') ORDER BY article_id, category_id',
            $ids,
        );
        $categoryIds = [];
        foreach ($links as $link) {
            $categoryIds[(int) $link['article_id']][] = (int) $link['category_id'];
        }

        return array_map(
            fn (array $row): Article => $this->map($row, $categoryIds[(int) $row['id']] ?? []),
            $rows,
        );
    }

    private function orderBy(ArticleSort $sort): string
    {
        return match ($sort) {
            ArticleSort::Date => 'a.published_at DESC, a.views DESC, a.id DESC',
            ArticleSort::Views => 'a.views DESC, a.published_at DESC, a.id DESC',
        };
    }

    private function fileId(mixed $value): ?int
    {
        if (!is_numeric($value)) {
            return null;
        }

        return (int) $value;
    }

    /**
     * @param array<string, mixed> $row
     * @param list<int> $categoryIds
     */
    private function map(array $row, array $categoryIds): Article
    {
        $publishedAt = $row['published_at'] ?? null;

        return new Article(
            (int) $row['id'],
            $this->fileId($row['file_id'] ?? null),
            (string) $row['name'],
            (string) $row['description'],
            (string) $row['text'],
            is_string($publishedAt) && $publishedAt !== '' ? new DateTimeImmutable($publishedAt) : null,
            (int) $row['views'],
            $categoryIds,
        );
    }
}
