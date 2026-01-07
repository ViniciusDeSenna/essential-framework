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
        $commandName = $input->arguments()[0];
        if ($commandName) {
            if (!$this->registry->has($commandName)) {
                $output->error("Command '{$commandName}' not found.\n");
                return 1;
            }
            $cmd = $this->registry->get($commandName);
            $name = $cmd->getName();
            $usage = $cmd->getUsage();
            $description = $cmd->getDescription();
            $output->write(sprintf(
                " %-" . (strlen($name) + 2) . "s %-" . (strlen($usage) + 2) . "s %s\n",
                $name,
                $usage,
                $description
            ));        
            return 0;
        }

        $output->write("Available commands:\n");
        foreach ($this->registry->all() as $cmd) {
            $output->write(sprintf("  %-16s %s\n", $cmd->getName(), $cmd->getDescription()));
        }
        return 0;

        return 0;
    }
}
