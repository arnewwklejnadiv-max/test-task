<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\File;

interface FileRepository
{
    public function add(File $file): File;

    /**
     * @param list<int> $ids
     *
     * @return array<int, File>
     */
    public function findByIds(array $ids): array;

    /** @return list<File> */
    public function all(): array;
}
