<?php

declare(strict_types=1);

namespace App\Infrastructure\View;

use App\Http\ViewInterface;

final readonly class FileRenderer implements ViewInterface
{
    public function __construct(private string $directory) {}

    public function render(string $view, array $data = []): string
    {
        $path = $this->directory . '/' . $view . '.html';
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new \RuntimeException('Шаблон не найден: ' . $view);
        }

        $rendered = preg_replace_callback(
            '/\{\{([a-zA-Z0-9_]+)\}\}/',
            static function (array $matches) use ($data): string {
                $value = $data[$matches[1]] ?? '';

                return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            },
            $contents,
        );

        return is_string($rendered) ? $rendered : $contents;
    }
}
