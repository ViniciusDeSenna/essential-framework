<?php

namespace Essential\Cli;

final class CommandRegistry
{
    /** @var array<string, CommandInterface> */
    private array $commands = [];

    public function register(CommandInterface $command): void
    {
        $this->commands[$command->getName()] = $command;
    }

    public function has(string $name): bool
    {
        return isset($this->commands[$name]);
    }

    public function get(string $name) {
        return $this->commands[$name];
    }

    /** @return CommandInterface[] */
    public function all(): array
    {
        return array_values($this->commands);
    }
}