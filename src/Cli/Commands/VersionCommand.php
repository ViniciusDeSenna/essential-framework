<?php

namespace Essential\Cli\Commands;

use Essential\Cli\AnsiColor;
use Essential\Cli\AnsiWeight;
use Essential\Cli\AnsiStyle;
use Essential\Cli\CommandInterface;
use Essential\Cli\Icon;
use Essential\Cli\Input;
use Essential\Cli\Output;

final class VersionCommand implements CommandInterface
{
    public function __construct(
        private string $name,
        private string $version,
        private string $url
    ) {}
    
    public function getName(): string
    {
        return 'version';
    }

    public function getDescription(): string
    {
        return 'Show CLI version';
    }

    public function getModule(): string
    {
        return 'CLI';
    }

    public function getUsage(): string
    {
        return 'ess --version';
    }

    public function execute(Input $input, Output $output): int
    {
        $output->clear();
        $output->newline(2);
        
        $this->renderLogo($output, $input->ansiEnabled());
        $output->newline();
        
        $this->renderInfo($output);
        $output->newline();
        
        $this->renderFooter($output);
        $output->newline(2);
        
        return 0;
    }

    private function renderLogo(Output $output, bool $ansi): void
    {
        if ($ansi) {
            $output->writeln("███████╗███████╗███████╗███████╗███╗   ██╗████████╗██╗ █████╗ ██╗     ");
            $output->writeln("██╔════╝██╔════╝██╔════╝██╔════╝████╗  ██║╚══██╔══╝██║██╔══██╗██║     ");
            $output->writeln("█████╗  ███████╗███████╗█████╗  ██╔██╗ ██║   ██║   ██║███████║██║     ");
            $output->writeln("██╔══╝  ╚════██║╚════██║██╔══╝  ██║╚██╗██║   ██║   ██║██╔══██║██║     ");
            $output->writeln("███████╗███████║███████║███████╗██║ ╚████║   ██║   ██║██║  ██║███████╗");
            $output->writeln("╚══════╝╚══════╝╚══════╝╚══════╝╚═╝  ╚═══╝   ╚═╝   ╚═╝╚═╝  ╚═╝╚══════╝");

        } else {
            $border = str_repeat('=', 50);
            $title = 'ESSENTIAL';
            
            $output->writeln($border);
            $output->writeln($title);
            $output->writeln($border);
        }
    }

    private function renderInfo(Output $output): void
    {
        $output->newline();

        // Version
        $output->line(
            "Version: ",
            AnsiColor::Cyan,
            [AnsiWeight::Bold],
            icon: Icon::Arrow
        );
        
        $output->line(
            "v{$this->version}",
            AnsiColor::Green,
            [AnsiWeight::Bold]
        );

        $output->newline();

        // URL
        $output->line(
            "Repository: ",
            AnsiColor::Cyan,
            [AnsiWeight::Bold],
            icon: Icon::Arrow
        );
        
        $output->line(
            $this->url,
            AnsiColor::Blue,
            styles: [AnsiStyle::Underline]
        );

        $output->newline();
    }

    private function renderFooter(Output $output): void
    {
        $year = date('Y');
        $output->line(
            "Built with ❤️  © {$year} All rights reserved",
            AnsiColor::Magenta
        );
    }
}