<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Category;
use App\Domain\Repository\CategoryRepository;
use App\Domain\Validation\CategoryValidator;

final class CategoryService
{
    public function __construct(
        private CategoryRepository $categories,
        private CategoryValidator $validator,
    ) {}

    public function create(Category $category): Category
    {
        $this->validator->assertValid($category);

        return $this->categories->add($category);
    }

    /** @return list<Category> */
    public function all(): array
    {
        return $this->categories->all();
    }
}
