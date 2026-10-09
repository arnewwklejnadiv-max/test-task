<?php

declare(strict_types=1);

namespace App\Domain\Validation;

use App\Domain\Entity\Article;
use App\Domain\Exception\ValidationException;

final class ArticleValidator
{
    public function assertValid(Article $article): void
    {
        $errors = [];
        $this->required($errors, 'Название статьи', $article->name, 255);
        $this->required($errors, 'Описание статьи', $article->description, null);
        $this->required($errors, 'Текст статьи', $article->text, null);
        $this->required($errors, 'Изображение статьи', $article->image, 255);
        if ($article->views < 0) {
            $errors[] = 'Количество просмотров не может быть отрицательным.';
        }
        foreach ($article->categoryIds as $categoryId) {
            if ($categoryId <= 0) {
                $errors[] = 'Идентификатор категории должен быть положительным числом.';
                break;
            }
        }
        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }

    /** @param list<string> $errors */
    private function required(array &$errors, string $field, string $value, ?int $max): void
    {
        $length = mb_strlen(trim($value));
        if ($length === 0) {
            $errors[] = $field . ' обязательно.';

            return;
        }
        if ($max !== null && $length > $max) {
            $errors[] = $field . ' не длиннее ' . $max . ' символов.';
        }
    }
}
