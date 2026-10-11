<?php

declare(strict_types=1);

namespace App\Http;

final class Response
{
    /** @param array<string, string> $headers */
    public function __construct(
        private int $status,
        private string $body,
        private array $headers = ['Content-Type' => 'text/html; charset=utf-8'],
    ) {}

    public static function html(string $body, int $status = 200): self
    {
        return new self($status, $body);
    }

    public static function text(string $body, int $status = 200): self
    {
        return new self($status, $body, ['Content-Type' => 'text/plain; charset=utf-8']);
    }

    public function status(): int
    {
        return $this->status;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function withCookie(string $name, string $value, int $maxAge): self
    {
        $copy = clone $this;
        $copy->headers['Set-Cookie'] = $name . '=' . $value
            . '; Max-Age=' . $maxAge
            . '; Path=/; HttpOnly; SameSite=Lax';

        return $copy;
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $this->body;
    }
}
