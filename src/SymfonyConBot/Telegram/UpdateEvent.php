<?php

declare(strict_types=1);

namespace App\SymfonyConBot\Telegram;

use App\SymfonyConBot\Telegram\Data\Update;
use Symfony\Component\RemoteEvent\RemoteEvent;

final class UpdateEvent extends RemoteEvent
{
    public function __construct(public readonly Update $update)
    {
        parent::__construct('telegram.update', (string) $update->updateId, []);
    }
}
