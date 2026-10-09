<?php

declare(strict_types=1);

namespace App\Http;

interface ViewInterface
{
    /** @param array<string, mixed> $data */
    public function render(string $view, array $data = []): string;
}
