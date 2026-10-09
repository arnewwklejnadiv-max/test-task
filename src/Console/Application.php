<?php

declare(strict_types=1);

namespace App\Console;

final class Application
{
    /** @param list<CommandInterface> $commands */
    public function __construct(private array $commands) {}

    /** @param list<string> $arguments */
    public function run(array $arguments): int
    {
        $name = $arguments[1] ?? '';
        foreach ($this->commands as $command) {
            if ($command->name() === $name) {
                return $command->execute(array_slice($arguments, 2));
            }
        }

        foreach ($this->commands as $command) {
            echo $command->name() . '  ' . $command->description() . "\n";
        }

        return $name === '' ? 0 : 1;
    }
}
