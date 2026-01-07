<?php

namespace Essential\Cli;

final class Output
{
    public function __construct(private bool $ansi) {};

    private function color(string $code, string $text): string
    {
        return $this->ansi ? "\033[{$code}m{$text}\033[0m" : $text;
    }

    public function write(string $text): void
    {
        echo $text;
    }

    public function info(string $text): void
    {
        $this->write($this->color('34', $text) . "\n");
    }


    public function error(string $text): void
    {
        $this->write($this->color('31', $text) . "\n");
    }
}