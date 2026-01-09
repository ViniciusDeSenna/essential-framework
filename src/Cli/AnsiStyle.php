<?php

namespace Essential\Cli;

enum AnsiStyle: int
{
    case Italic        = 3;
    case Underline     = 4;
    case BlinkSlow     = 5;
    case BlinkRapid    = 6;
    case Inverse       = 7;
    case Hidden        = 8;
    case Strikethrough = 9;
}