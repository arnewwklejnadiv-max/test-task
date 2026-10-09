<?php

declare(strict_types=1);

namespace Database\Seed;

use App\Domain\Service\CategoryService;
use RuntimeException;

final class DatabaseSeeder
{
    public function __construct(
        private CategorySeeder $categories,
        private FileSeeder $files,
        private ArticleSeeder $articles,
        private CategoryService $existing,
    ) {}

    public function run(): void
    {
        if ($this->existing->all() !== []) {
            throw new RuntimeException('В таблице categories уже есть строки. Повторное заполнение отменено.');
        }

        $this->categories->run();
        $this->files->run();
        $this->articles->run();
    }
}
