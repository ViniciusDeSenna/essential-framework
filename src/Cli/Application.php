<?php

namespace Essential\Cli;

use Essential\Cli\Exceptions\UsageException;

final class Application
{
    public function __construct( private CommandRegistry $registry ) {}

    public function run(array $argv): int
{
    $input = new Input($argv);
    $output = new Output($input->ansiEnabled());

    if ($input->has('--version', '-v')) {
        return $this->registry->get('version')->execute($input, $output);
    }

    if ($input->has('--help', '-h')) {
        return $this->registry->get('help')->execute($input, $output);
    }

    $cmd = $input->shiftCommand();

    if (!$cmd || !$this->registry->has($cmd)) {
        $output->error("Command '{$cmd}' not found");
        return 1;
    }

    try {
        return $this->registry->get($cmd)->execute($input, $output);
    } catch (UsageException $e) {
        $output->error($e->getMessage());
        return 2;
    } catch (\Throwable $e) {
        $output->error("Internal error");
        return 1;
    }
}

}