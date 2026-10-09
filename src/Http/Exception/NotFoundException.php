<?php

declare(strict_types=1);

namespace App\Http\Exception;

final class NotFoundException extends HttpException
{
    public function __construct()
    {
        parent::__construct(404, 'Страница не найдена');
    }
}
