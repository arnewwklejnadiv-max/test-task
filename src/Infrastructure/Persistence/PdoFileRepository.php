<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\File;
use App\Domain\Repository\FileRepository;
use App\Infrastructure\Database\ConnectionInterface;

final class PdoFileRepository implements FileRepository
{
    public function __construct(private ConnectionInterface $connection) {}

    public function add(File $file): File
    {
        $this->connection->execute(
            'INSERT INTO files (path, name) VALUES (:path, :name)',
            [
                'path' => $file->path,
                'name' => $file->name,
            ],
        );

        return new File($this->connection->lastInsertId(), $file->path, $file->name);
    }

    /**
     * @param list<int> $ids
     *
     * @return array<int, File>
     */
    public function findByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter($ids, static fn (int $id): bool => $id > 0)));
        if ($ids === []) {
            return [];
        }

        $files = [];
        foreach ($this->connection->fetchAll(
            'SELECT id, path, name FROM files WHERE id IN (' . implode(', ', array_fill(0, count($ids), '?')) . ')',
            $ids,
        ) as $row) {
            $files[(int) $row['id']] = $this->map($row);
        }

        return $files;
    }

    /** @return list<File> */
    public function all(): array
    {
        return array_map(
            $this->map(...),
            $this->connection->fetchAll('SELECT id, path, name FROM files ORDER BY id'),
        );
    }

    /** @param array<string, mixed> $row */
    private function map(array $row): File
    {
        return new File(
            (int) $row['id'],
            (string) $row['path'],
            (string) $row['name'],
        );
    }
}
