<?php

declare(strict_types=1);

namespace App\SymfonyConBot;

final readonly class ChatBot
{
    public function message(int $userId, string $content): string
    {
        return sprintf('You said "%s".', $content);
    }
}
