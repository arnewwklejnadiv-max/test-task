<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

final class Env
{
    public static function load(string $path): void
    {
        if (!is_file($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            throw new \RuntimeException('Не удалось прочитать ' . $path);
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if ($key === '' || self::has($key)) {
                continue;
            }

            $length = strlen($value);
            if ($length >= 2) {
                $quote = $value[0];
                if (($quote === '"' || $quote === '\'') && $value[$length - 1] === $quote) {
                    $value = substr($value, 1, -1);
                }
            }

            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    /** @param list<string> $keys */
    public static function require(array $keys): void
    {
        foreach ($keys as $key) {
            self::string($key);
        }
    }

    public static function string(string $key): string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if (!is_string($value) || $value === '') {
            throw new \RuntimeException($key . ' не задан');
        }

        return $value;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        if (!self::has($key)) {
            return $default;
        }

        return in_array(strtolower(self::string($key)), ['1', 'true', 'yes', 'on'], true);
    }

    public static function int(string $key): int
    {
        $value = self::string($key);
        if (!ctype_digit($value)) {
            throw new \RuntimeException($key . ' должен быть целым числом');
        }

        return (int) $value;
    }

    private static function has(string $key): bool
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        return is_string($value) && $value !== '';
    }
}
