<?php

declare(strict_types=1);

namespace Database\Seed;

use App\Domain\Entity\Article;
use App\Domain\Entity\Category;
use App\Domain\Service\ArticleService;
use App\Domain\Service\CategoryService;
use App\Domain\Service\FileService;
use DateTimeImmutable;

final class ArticleSeeder
{
    public const COUNT = 24;

    public function __construct(
        private ArticleService $articles,
        private CategoryService $categories,
        private FileService $files,
    ) {}

    public function run(): void
    {
        $categories = $this->categories->all();
        $categoryCount = count($categories);
        if ($categoryCount === 0) {
            return;
        }

        $fileIds = $this->fileIds();
        for ($number = 1; $number <= self::COUNT; ++$number) {
            $this->articles->create(new Article(
                null,
                $fileIds[sprintf('article-%d.jpg', $number)] ?? null,
                sprintf('Статья %d', $number),
                sprintf('Описание статьи %d', $number),
                sprintf('Текст статьи %d', $number),
                new DateTimeImmutable('2026-01-01 +' . ($number - 1) . ' days'),
                0,
                $this->categoryIds($categories, $number, $categoryCount),
            ));
        }
    }

    /** @return array<string, int> */
    private function fileIds(): array
    {
        $fileIds = [];
        foreach ($this->files->all() as $file) {
            if ($file->id === null) {
                continue;
            }

            $fileIds[$file->name] = $file->id;
        }

        return $fileIds;
    }

    /**
     * @param list<Category> $categories
     *
     * @return list<int>
     */
    private function categoryIds(array $categories, int $number, int $categoryCount): array
    {
        $ids = [$this->id($categories[($number - 1) % $categoryCount])];
        if ($number % 2 !== 0) {
            return $ids;
        }

        $secondId = $this->id($categories[$number % $categoryCount]);
        if ($secondId !== $ids[0]) {
            $ids[] = $secondId;
        }

        return $ids;
    }

    private function id(Category $category): int
    {
        if ($category->id === null) {
            throw new \RuntimeException('Category id is missing');
        }

        return $category->id;
    }
}
