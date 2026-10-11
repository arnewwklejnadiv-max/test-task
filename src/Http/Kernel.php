<?php

declare(strict_types=1);

namespace App\Http;

use App\Http\Exception\MethodNotAllowedException;
use App\Http\Exception\NotFoundException;
use App\Http\Handler\MethodNotAllowedExceptionHandler;
use App\Http\Handler\NotFoundExceptionHandler;
use App\Http\Handler\UnhandledExceptionHandler;
use Throwable;

final class Kernel
{
    public function __construct(
        private Router $router,
        private NotFoundExceptionHandler $notFound,
        private MethodNotAllowedExceptionHandler $methodNotAllowed,
        private UnhandledExceptionHandler $unhandled,
    ) {}

    public function handle(Request $request): Response
    {
        try {
            return $this->router->dispatch($request);
        } catch (NotFoundException $exception) {
            return $this->notFound->handle($exception);
        } catch (MethodNotAllowedException $exception) {
            return $this->methodNotAllowed->handle($exception);
        } catch (Throwable $exception) {
            return $this->unhandled->handle($exception);
        }
    }
}
