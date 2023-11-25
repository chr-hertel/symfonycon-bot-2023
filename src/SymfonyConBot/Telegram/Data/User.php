<?php

declare(strict_types=1);

namespace App\SymfonyConBot\Telegram\Data;

final class User
{
    public int $id;
    public bool $isBot;
    public string $firstName;
    public string $lastName;
    public string $languageCode;
}
