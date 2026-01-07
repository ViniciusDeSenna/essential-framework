<?php

namespace Essential\Cli\Commands;

use Essential\Cli\CommandInterface;
use Essential\Cli\CommandRegistry;
use Essential\Cli\Input;
use Essential\Cli\Output;

final class HelpCommand implements CommandInterface
{
    public function __construct(private CommandRegistry $registry) {}

    public function getName(): string
    {
        return 'help';
    }

    public function getDescription(): string
    {
        return 'Show available commands';
    }

    public function getUsage(): string
    {
        return 'ess help [command]';
    }

    public function execute(Input $input, Output $output): int
    {
        $args = $input->arguments();
        $commandName = $args[0] ?? null;

        if ($commandName) {
            return $this->showCommandHelp($commandName, $output);
        }

        return $this->showAllCommands($output);
    }

    private function showCommandHelp(string $name, Output $output): int
    {
        if (!$this->registry->has($name)) {
            $output->error("Command '{$name}' not found.\n");
            return 1;
        }

        $cmd = $this->registry->get($name);

        $commands = $this->registry->all();
        $maxName = max(array_map(fn($c) => strlen($c->getName()), $commands));
        $maxUsage = max(array_map(fn($c) => strlen($c->getUsage()), $commands));

        $output->write("Command details:\n");
        $output->write(sprintf(
            "  %-{$maxName}s  '%-{$maxUsage}s'  %s\n",
            $cmd->getName(),
            $cmd->getUsage(),
            $cmd->getDescription()
        ));

        return 0;
    }

    private function showAllCommands(Output $output): int
    {
        $output->write("Available commands:\n");

        $commands = $this->registry->all();
        $maxLength = $this->getMaxNameLength($commands);

        foreach ($commands as $cmd) {
            $output->write(sprintf(
                "  %-{$maxLength}s  %s\n",
                $cmd->getName(),
                $cmd->getDescription()
            ));
        }

        return 0;
    }

    private function getMaxNameLength(array $commands): int
    {
        $lengths = array_map(fn($cmd) => strlen($cmd->getName()), $commands);
        return max($lengths ?: [0]);
    }
}