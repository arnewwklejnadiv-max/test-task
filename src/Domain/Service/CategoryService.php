<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Category;
use App\Domain\Repository\CategoryRepository;

final class CategoryService
{
    public const PAGE_SIZE = 10;

    public function __construct(private CategoryRepository $categories) {}

    public function create(Category $category): Category
    {
        return $this->categories->add($category);
    }

    /** @return list<Category> */
    public function all(): array
    {
        return $this->categories->all();
    }

    public function find(int $id): ?Category
    {
        return $this->categories->find($id);
    }

    /**
     * @return array{categories: list<Category>, nextOffset: int|null}
     */
    public function withArticles(int $offset): array
    {
        $offset = max(0, $offset);
        $categories = $this->categories->withArticles(self::PAGE_SIZE + 1, $offset);
        $more = count($categories) > self::PAGE_SIZE;
        if ($more) {
            array_pop($categories);
        }

        return [
            'categories' => $categories,
            'nextOffset' => $more ? $offset + self::PAGE_SIZE : null,
        ];
    }
}
