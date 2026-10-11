<?php

declare(strict_types=1);

namespace App\Http\Session;

use App\Domain\Repository\SessionRepository;
use App\Domain\Repository\TokenRepository;
use App\Domain\Entity\Session;
use App\Http\Request;
use DateTimeImmutable;

final class ViewSession
{
    public const COOKIE = 'session_id';

    public const LIFETIME = 86400;

    public const HEADER = 'X-CSRF-TOKEN';

    public function __construct(
        private SessionRepository $sessions,
        private TokenRepository $tokens,
    ) {}

    public function open(Request $request): OpenedSession
    {
        $current = $this->current($request);
        $cookie = false;
        if ($current === null || $current->id === null) {
            $current = $this->sessions->add(new DateTimeImmutable('+' . self::LIFETIME . ' seconds'));
            $cookie = true;
        }

        $token = $this->tokens->add(
            bin2hex(random_bytes(32)),
            new DateTimeImmutable('+' . self::LIFETIME . ' seconds'),
        );
        $this->sessions->addToken((int) $current->id, $token->token);

        return new OpenedSession($current, $token->token, $cookie);
    }

    public function take(Request $request): ViewAttempt
    {
        $session = $this->current($request);
        $header = $request->header(self::HEADER);
        if ($session === null || $session->id === null || $header === '' || !$this->sessions->hasToken($session->id, $header)) {
            return ViewAttempt::rejected();
        }

        $now = new DateTimeImmutable();
        if ($this->tokens->consume($header, $now)) {
            return ViewAttempt::fresh();
        }

        $existing = $this->tokens->find($header);
        if ($existing !== null && $existing->usedAt !== null) {
            return ViewAttempt::used();
        }

        return ViewAttempt::rejected();
    }

    private function current(Request $request): ?Session
    {
        $id = $request->cookie(self::COOKIE);
        if (!ctype_digit($id) || $id === '0') {
            return null;
        }

        $session = $this->sessions->find((int) $id);
        if ($session === null || $session->expiresAt <= new DateTimeImmutable()) {
            return null;
        }

        return $session;
    }
}
