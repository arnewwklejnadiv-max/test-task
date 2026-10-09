<?php

declare(strict_types=1);

namespace App\Http;

final class Request
{
    /** @param array<string, mixed> $query */
    public function __construct(
        private string $method,
        private string $path,
        private array $query,
    ) {}

    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url(is_string($uri) ? $uri : '/', PHP_URL_PATH);
        if (!is_string($path) || $path === '') {
            $path = '/';
        }
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        return new self(
            is_string($method) ? strtoupper($method) : 'GET',
            $path,
            $_GET,
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(string $key): mixed
    {
        return $this->query[$key] ?? null;
    }
}
