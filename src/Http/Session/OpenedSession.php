<?php

declare(strict_types=1);

namespace App\Http\Session;

use App\Domain\Entity\Session;

final readonly class OpenedSession
{
    public function __construct(
        public Session $session,
        public string $viewToken,
        public bool $cookie,
    ) {}
}
