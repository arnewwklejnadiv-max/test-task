<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\File;
use App\Domain\Repository\FileRepository;

final class FileService
{
    public function __construct(private FileRepository $files) {}

    public function create(File $file): File
    {
        return $this->files->add($file);
    }

    public function find(int $id): ?File
    {
        return $this->files->find($id);
    }

    /** @return list<File> */
    public function all(): array
    {
        return $this->files->all();
    }
}
