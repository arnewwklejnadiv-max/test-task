<?php

declare(strict_types=1);

namespace App\Http\Handler;

use App\Http\Exception\NotFoundException;
use App\Http\Response;

final class NotFoundExceptionHandler
{
    public function handle(NotFoundException $exception): Response
    {
        return Response::text('Страница не найдена', $exception->statusCode());
    }
}
