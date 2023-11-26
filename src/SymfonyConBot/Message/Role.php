<?php

declare(strict_types=1);

namespace App\SymfonyConBot\Message;

enum Role: string
{
    case System = 'system';
    case Assistant = 'assistant';
    case User = 'user';
    case FunctionCall = 'function';
}
