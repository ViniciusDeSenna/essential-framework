<?php

namespace Essential\Cli;

final final class Input
{
    private array $args;
    private ?string $command = null;

    public function __construct(array $argv) {
        array_shift($argv);
        $this->args = $argv;
    }

    public function shiftCommand(): ?string
    {
        if ($this->command !== null)
            return $this->command;

        $this->command = $this->args[0] ?? null;
        
        if ($this->command !== null)
            return array_shift($this->args);
        
        return $this->command;
    }

    public function has(string ...$flags): bool
    {
        foreach ($flags as $f) {
            if (in_array($f, $this->args, true)) {
                return true;
            }
        }
        return false;
    }

    public function options(): array
    {
        return array_values(array_filter($this->args, fn($a) => str_starts_with($a, '-')));
    }

    public function arguments(): array
    {
        return array_values(array_filter($this->args, fn($a) => !str_starts_with($a, '-')));
    }

    public function ansiEnabled(): bool {
        if ($this->has('--no-ansi')) return false;
        if ($this->has('--ansi')) return true;
        return function_exists('posix_isatty') && @posix_isatty(STDOUT);
    }

}
