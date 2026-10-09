<?php

declare(strict_types=1);

namespace Database\Seed;

use App\Domain\Entity\File;
use App\Domain\Service\FileService;

final class FileSeeder
{
    public const COUNT = 24;

    public function __construct(private FileService $files) {}

    public function run(): void
    {
        for ($number = 1; $number <= self::COUNT; ++$number) {
            if ($number % 6 === 0) {
                continue;
            }

            $this->files->create(new File(null, 'images', sprintf('article-%d.jpg', $number)));
        }
    }
}
