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
