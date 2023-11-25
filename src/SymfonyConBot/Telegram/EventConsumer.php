<?php

declare(strict_types=1);

namespace App\SymfonyConBot\Telegram;

use App\SymfonyConBot\ChatBot;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('telegram.update')]
final readonly class EventConsumer implements ConsumerInterface
{
    public function __construct(
        private ChatBot $chatBot,
        private ChatterInterface $chatter,
    ) {
    }

    public function consume(UpdateEvent|RemoteEvent $event): void
    {
        assert($event instanceof UpdateEvent);

        $update = $event->update;
        $user = $update->getSender();
        $text = $update->getMessageText();

        $response = $this->chatBot->message($user->id, $text);

        $this->respond($update->getChatId(), $response);
    }

    private function respond(int $chatId, string $response): void
    {
        $options = (new TelegramOptions())->chatId((string) $chatId);

        $this->chatter->send(
            (new ChatMessage($response))->options($options)
        );
    }
}
