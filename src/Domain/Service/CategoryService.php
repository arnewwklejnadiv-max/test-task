<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Category;
use App\Domain\Repository\CategoryRepository;

final class CategoryService
{
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
}
