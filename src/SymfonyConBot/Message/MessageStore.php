<?php

declare(strict_types=1);

namespace App\SymfonyConBot\Message;

use App\SymfonyConBot\Exception\MessageBagNotFoundException;
use Psr\Cache\CacheItemPoolInterface;

final readonly class MessageStore
{
    public function __construct(private CacheItemPoolInterface $cache)
    {
    }

    public function load(int $userId): MessageBag
    {
        $item = $this->cache->getItem('messages_'.$userId);

        if (!$item->isHit()) {
            throw new MessageBagNotFoundException();
        }

        return $item->get();
    }

    public function save(MessageBag $messages, int $userId): void
    {
        $item = $this->cache->getItem('messages_'.$userId);
        $item->set($messages);
        $this->cache->save($item);
    }
}
