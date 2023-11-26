<?php

declare(strict_types=1);

namespace App\SymfonyConBot\Schedule;

use App\SymfonyConBot\Schedule\Crawler\Client;
use App\SymfonyConBot\Schedule\Crawler\Parser;
use Doctrine\ORM\EntityManagerInterface;

final class Crawler
{
    public function __construct(
        private readonly Client $client,
        private readonly Parser $parser,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function loadSchedule(): void
    {
        $response = $this->client->getSchedule();

        foreach ($this->parser->extractSlots($response) as $slot) {
            $this->entityManager->persist($slot);
        }

        $this->entityManager->flush();
    }
}
