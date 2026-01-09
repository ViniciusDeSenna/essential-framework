<?php

namespace Essential\Cli;

enum AnsiColor: int
{
    case Default = 39;

    case Black   = 30;
    case Red     = 31;
    case Green   = 32;
    case Yellow  = 33;
    case Blue    = 34;
    case Magenta = 35;
    case Cyan    = 36;
    case White   = 37;

    case BrightBlack   = 90;
    case BrightRed     = 91;
    case BrightGreen   = 92;
    case BrightYellow  = 93;
    case BrightBlue    = 94;
    case BrightMagenta = 95;
    case BrightCyan    = 96;
    case BrightWhite   = 97;
}