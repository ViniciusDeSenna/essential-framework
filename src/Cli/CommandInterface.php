<?php

namespace Essential\Cli;

interface CommandInterface
{
    public function getName(): string;
    public function getDescription(): string;
    public function getModule(): string;
    public function getUsage(): string;
    public function execute(Input $input, Output $output): int;
}