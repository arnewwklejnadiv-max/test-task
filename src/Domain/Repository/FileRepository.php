<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\File;

interface FileRepository
{
    public function add(File $file): File;

    public function find(int $id): ?File;

    /** @return list<File> */
    public function all(): array;
}
