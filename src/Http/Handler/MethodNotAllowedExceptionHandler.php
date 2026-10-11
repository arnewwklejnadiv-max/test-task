<?php

declare(strict_types=1);

namespace App\Http\Handler;

use App\Http\Exception\MethodNotAllowedException;
use App\Http\Response;

final class MethodNotAllowedExceptionHandler
{
    public function handle(MethodNotAllowedException $exception): Response
    {
        return Response::text('Метод не поддерживается', $exception->statusCode());
    }
}
