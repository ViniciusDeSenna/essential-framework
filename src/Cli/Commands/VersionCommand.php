<?php

namespace Essential\Cli\Commands;

use Essential\Cli\CommandInterface;
use Essential\Cli\Input;
use Essential\Cli\Output;

final class VersionCommand implements CommandInterface
{
    public function __construct(private string $name, private string $version) {}
    
    public function getName(): string
    {
        return 'version';
    }

    public function getDescription(): string
    {
        return 'Show CLI version';
    }

    public function getUsage(): string
    {
        return 'ess --version';
    }

    public function execute(Input $input, Output $output): int
    {
        $output->write($this->name . ' ' . $this->version . "\n");
        return 0;
    }
    
}
