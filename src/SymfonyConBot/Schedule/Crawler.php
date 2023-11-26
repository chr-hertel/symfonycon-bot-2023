<?php

declare(strict_types=1);

namespace App\SymfonyConBot\Schedule;

use App\Entity\Slot;
use App\SymfonyConBot\Schedule\Crawler\Client;
use App\SymfonyConBot\Schedule\Crawler\Parser;

final class Crawler
{
    public function __construct(
        private readonly Client $client,
        private readonly Parser $parser,
    ) {
    }

    /**
     * @return list<Slot>
     */
    public function loadSchedule(): array
    {
        return $this->parser->extractSlots(
            $this->client->getSchedule()
        );
    }
}
