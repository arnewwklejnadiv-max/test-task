<?php

declare(strict_types=1);

namespace App\Http\Exception;

final class MethodNotAllowedException extends HttpException
{
    /** @param list<string> $allowed */
    public function __construct(array $allowed)
    {
        $message = 'Метод не поддерживается';
        if ($allowed !== []) {
            $message .= '. Допустимые методы: ' . implode(', ', $allowed);
        }

        parent::__construct(405, $message);
    }
}
