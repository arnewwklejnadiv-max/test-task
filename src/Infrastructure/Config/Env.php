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
            [$key, $value] = explode('=', $line, 2);
            $_ENV[$key] = $value;
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
        $value = $_ENV[$key] ?? '';
        if ($value === '') {
            throw new \RuntimeException($key . ' не задан');
        }

        return $value;
    }

    public static function bool(string $key): bool
    {
        return self::string($key) === 'true';
    }

    public static function int(string $key): int
    {
        $value = self::string($key);
        if (!ctype_digit($value)) {
            throw new \RuntimeException($key . ' должен быть целым числом');
        }

        return (int) $value;
    }
}
