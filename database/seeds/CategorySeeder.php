<?php

declare(strict_types=1);

namespace Database\Seed;

use App\Domain\Entity\Category;
use App\Domain\Service\CategoryService;

final class CategorySeeder
{
    public const COUNT = 10;

    public function __construct(private CategoryService $categories) {}

    public function run(): void
    {
        for ($number = 1; $number <= self::COUNT; ++$number) {
            $this->categories->create(new Category(
                null,
                sprintf('Категория %d', $number),
                sprintf('Описание категории %d', $number),
            ));
        }
    }
}
