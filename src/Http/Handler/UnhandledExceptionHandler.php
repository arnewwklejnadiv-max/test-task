<?php

declare(strict_types=1);

namespace App\Http\Handler;

use App\Http\Response;
use Throwable;

final class UnhandledExceptionHandler
{
    public function handle(Throwable $exception): Response
    {
        error_log($exception->getMessage());

        return Response::text('Внутренняя ошибка', 500);
    }
}
