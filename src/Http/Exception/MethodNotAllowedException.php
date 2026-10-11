<?php

declare(strict_types=1);

namespace App\Http\Exception;

final class MethodNotAllowedException extends HttpException
{
    public function __construct()
    {
        parent::__construct(405, 'Метод не поддерживается');
    }
}
