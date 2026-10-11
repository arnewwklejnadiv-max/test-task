<?php

declare(strict_types=1);

namespace App\Http;

final class Request
{
    /** @param array<string, mixed> $query */
    /** @param array<string, string> $params */
    /** @param array<string, string> $cookies */
    /** @param array<string, string> $headers */
    public function __construct(
        private string $method,
        private string $path,
        private array $query,
        private array $params = [],
        private array $cookies = [],
        private array $headers = [],
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
            [],
            self::strings($_COOKIE),
            self::headers($_SERVER),
        );
    }

    /**
     * @param array<mixed> $source
     *
     * @return array<string, string>
     */
    private static function strings(array $source): array
    {
        $values = [];
        foreach ($source as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $values[$key] = $value;
            }
        }

        return $values;
    }

    /**
     * @param array<mixed> $server
     *
     * @return array<string, string>
     */
    private static function headers(array $server): array
    {
        $headers = [];
        foreach ($server as $key => $value) {
            if (is_string($key) && str_starts_with($key, 'HTTP_') && is_string($value)) {
                $headers[$key] = $value;
            }
        }

        return $headers;
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

    /** @param array<string, string> $params */
    public function withParams(array $params): self
    {
        $copy = clone $this;
        $copy->params = $params;

        return $copy;
    }

    public function param(string $key): string
    {
        return $this->params[$key] ?? '';
    }

    public function cookie(string $key): string
    {
        return $this->cookies[$key] ?? '';
    }

    public function header(string $name): string
    {
        return $this->headers['HTTP_' . strtoupper(str_replace('-', '_', $name))] ?? '';
    }
}
