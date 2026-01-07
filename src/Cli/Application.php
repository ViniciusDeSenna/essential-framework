<?php

namespace Essential\Cli;

final class Application
{
    public function __construct(
        private string $name,
        private string $version,
        private CommandRegistry $registry
    ) {}

    public function run(array $argv): int
    {
        $input = new Input($argv)
        $output = new Output($input->ansiIs);
    }
}