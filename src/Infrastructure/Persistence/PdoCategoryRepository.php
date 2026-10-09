<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\Category;
use App\Domain\Repository\CategoryRepository;
use App\Infrastructure\Database\ConnectionInterface;

final class PdoCategoryRepository implements CategoryRepository
{
    public function __construct(private ConnectionInterface $connection) {}

    public function add(Category $category): Category
    {
        $this->connection->execute(
            'INSERT INTO categories (name, description) VALUES (:name, :description)',
            [
                'name' => $category->name,
                'description' => $category->description,
            ],
        );

        return new Category($this->connection->lastInsertId(), $category->name, $category->description);
    }

    public function find(int $id): ?Category
    {
        $row = $this->connection->fetch(
            'SELECT id, name, description FROM categories WHERE id = :id',
            ['id' => $id],
        );
        if ($row === null) {
            return null;
        }

        return $this->map($row);
    }

    public function all(): array
    {
        return array_map(
            $this->map(...),
            $this->connection->fetchAll('SELECT id, name, description FROM categories ORDER BY id'),
        );
    }

    public function findByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter($ids, static fn (int $id): bool => $id > 0)));
        if ($ids === []) {
            return [];
        }

        return array_map(
            $this->map(...),
            $this->connection->fetchAll(
                'SELECT id, name, description FROM categories WHERE id IN (' . implode(', ', array_fill(0, count($ids), '?')) . ') ORDER BY name',
                $ids,
            ),
        );
    }

    public function withArticles(int $limit, int $offset): array
    {
        return array_map(
            $this->map(...),
            $this->connection->fetchAll(
                'SELECT c.id, c.name, c.description
                 FROM categories c
                 WHERE EXISTS (
                     SELECT 1 FROM article_category ac WHERE ac.category_id = c.id
                 )
                 ORDER BY c.name
                 LIMIT ' . $limit . ' OFFSET ' . $offset,
            ),
        );
    }

    /** @param array<string, mixed> $row */
    private function map(array $row): Category
    {
        return new Category(
            (int) $row['id'],
            (string) $row['name'],
            (string) $row['description'],
        );
    }
}
