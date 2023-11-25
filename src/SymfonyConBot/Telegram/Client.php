<?php

declare(strict_types=1);

namespace App\SymfonyConBot\Telegram;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Client
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private UrlGeneratorInterface $urlGenerator,
        private string $baseUrl,
        private string $token,
    ) {
    }

    public function registerWebhook(): void
    {
        $url = $this->baseUrl.$this->urlGenerator->generate('_webhook_controller', ['type' => 'telegram.update']);

        $this->callEndpoint('setWebhook', ['url' => $url]);
    }

    /**
     * @phpstan-param array<string, mixed> $payload
     */
    private function callEndpoint(string $endpoint, array $payload): void
    {
        $endpoint = sprintf('https://api.telegram.org/bot%s/%s', $this->token, $endpoint);

        $this->httpClient->request('POST', $endpoint, ['json' => $payload]);
    }
}
