<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

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

        return $this->map($row, $this->categoryIds($id));
    }

    public function all(): array
    {
        $rows = $this->connection->fetchAll(
            'SELECT id, file_id, name, description, `text`, published_at, views FROM articles ORDER BY id',
        );
        if ($rows === []) {
            return [];
        }

        $params = [];
        $placeholders = [];
        foreach ($rows as $index => $row) {
            $key = 'id' . $index;
            $placeholders[] = ':' . $key;
            $params[$key] = (int) $row['id'];
        }
        $links = $this->connection->fetchAll(
            'SELECT article_id, category_id FROM article_category WHERE article_id IN ('
            . implode(', ', $placeholders)
            . ') ORDER BY article_id, category_id',
            $params,
        );
        $categoryIds = [];
        foreach ($links as $link) {
            $articleId = $link['article_id'] ?? null;
            $categoryId = $link['category_id'] ?? null;
            if (!is_numeric($articleId) || !is_numeric($categoryId)) {
                continue;
            }
            $categoryIds[(int) $articleId][] = (int) $categoryId;
        }

        return array_map(
            fn (array $row): Article => $this->map($row, $categoryIds[(int) $row['id']] ?? []),
            $rows,
        );
    }

    public function incrementViews(int $id): void
    {
        $this->connection->execute(
            'UPDATE articles SET views = views + 1 WHERE id = :id',
            ['id' => $id],
        );
    }

    /** @return list<int> */
    private function categoryIds(int $articleId): array
    {
        $rows = $this->connection->fetchAll(
            'SELECT category_id FROM article_category WHERE article_id = :article_id ORDER BY category_id',
            ['article_id' => $articleId],
        );

        return array_map(static fn (array $row): int => (int) $row['category_id'], $rows);
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
