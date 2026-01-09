<?php

namespace Essential\Cli;

final class Output
{
    public function __construct(private bool $ansi) {}

    private function style(array $codes, string $text): string
    {
        if (!$this->ansi || empty($codes)) {
            return $text;
        }

        $seq = implode(';', $codes);
        return "\033[{$seq}m{$text}\033[0m";
    }

    private function collectCodes(
        ?AnsiColor $color,
        ?AnsiColor $bg,
        array $weights,
        array $styles
    ): array {
        $codes = [];

        if ($color) {
            $codes[] = $color->value;
        }

        if ($bg) {
            $codes[] = $bg->value;
        }

        foreach ($weights as $weight) {
            $codes[] = $weight->value;
        }

        foreach ($styles as $style) {
            $codes[] = $style->value;
        }

        return $codes;
    }

    private function format(
        string $text,
        ?AnsiColor $color = null,
        array $weights = [],
        array $styles = [],
        ?AnsiColor $bg = null,
        ?Icon $icon = null
    ): string {
        $codes = $this->collectCodes($color, $bg, $weights, $styles);
        $prefix = $icon?->value ?? '';
        
        return $this->style($codes, $prefix . $text);
    }

    public function write(string $text): void
    {
        echo $text;
    }

    public function writeln(string $text = ''): void
    {
        $this->write($text . PHP_EOL);
    }

    public function line(
        string $text,
        ?AnsiColor $color = null,
        array $weights = [],
        array $styles = [],
        ?AnsiColor $bg = null,
        ?Icon $icon = null
    ): void {
        $formatted = $this->format($text, $color, $weights, $styles, $bg, $icon);
        $this->write($formatted);
    }

    public function info(string $text): void
    {
        $this->line($text, AnsiColor::Cyan, icon: Icon::Arrow);
    }

    public function success(string $text): void
    {
        $this->line($text, AnsiColor::Green, [AnsiWeight::Bold], icon: Icon::Success);
    }

    public function warning(string $text): void
    {
        $this->line($text, AnsiColor::Yellow, [AnsiWeight::Bold], icon: Icon::Warning);
    }

    public function error(string $text): void
    {
        $this->line($text, AnsiColor::Red, [AnsiWeight::Bold], icon: Icon::Error);
    }

    public function clear(): void
    {
        if ($this->ansi) {
            $this->write("\033[2J\033[H");
        }
    }

    public function newline(int $count = 1): void
    {
        $this->write(str_repeat(PHP_EOL, $count));
    }
}