<?php

declare(strict_types=1);

namespace App\Domain\Validation;

use App\Domain\Entity\Category;
use App\Domain\Exception\ValidationException;

final class CategoryValidator
{
    public function assertValid(Category $category): void
    {
        $errors = [];
        $name = mb_strlen(trim($category->name));
        if ($name === 0) {
            $errors[] = 'Название категории обязательно.';
        } elseif ($name > 255) {
            $errors[] = 'Название категории не длиннее 255 символов.';
        }
        if (mb_strlen(trim($category->description)) === 0) {
            $errors[] = 'Описание категории обязательно.';
        }
        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }
}
