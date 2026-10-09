<?php

declare(strict_types=1);

namespace App\Domain\Exception;

final class ValidationException extends \RuntimeException
{
    /** @param list<string> $errors */
    public function __construct(private array $errors)
    {
        parent::__construct(implode(' ', $errors));
    }

    /** @return list<string> */
    public function errors(): array
    {
        return $this->errors;
    }
}
