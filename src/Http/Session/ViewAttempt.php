<?php

declare(strict_types=1);

namespace App\Http\Session;

final readonly class ViewAttempt
{
    private function __construct(
        public bool $accepted,
        public bool $fresh,
    ) {}

    public static function rejected(): self
    {
        return new self(false, false);
    }

    public static function fresh(): self
    {
        return new self(true, true);
    }

    public static function used(): self
    {
        return new self(true, false);
    }
}
