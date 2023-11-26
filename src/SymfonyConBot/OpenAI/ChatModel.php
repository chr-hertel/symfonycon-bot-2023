<?php

declare(strict_types=1);

namespace App\SymfonyConBot\OpenAI;

use App\SymfonyConBot\Message\MessageBag;

final readonly class ChatModel
{
    public function __construct(
        private Client $client,
        private string $model = 'gpt-4',
        private float $temperature = 1.0,
    ) {
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    public function call(MessageBag $messages, array $options = []): array
    {
        $body = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $this->temperature,
        ];

        return $this->client->request('chat/completions', array_merge($body, $options));
    }
}
