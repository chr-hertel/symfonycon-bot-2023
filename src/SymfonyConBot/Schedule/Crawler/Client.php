<?php

declare(strict_types=1);

namespace App\SymfonyConBot\Schedule\Crawler;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Client
{
    public function __construct(
        private HttpClientInterface $httpClient
    ) {
    }

    public function getSchedule(): string
    {
        $response = $this->httpClient->request('GET', 'https://live.symfony.com/2023-brussels-con/schedule');

        return $response->getContent();
    }
}
