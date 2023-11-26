<?php

declare(strict_types=1);

namespace App\SymfonyConBot\Message;

/**
 * @template-extends \ArrayObject<int, Message>
 */
final class MessageBag extends \ArrayObject implements \JsonSerializable
{
    public function __construct(Message ...$messages)
    {
        parent::__construct(array_values($messages));
    }

    public function with(Message $message): self
    {
        $messages = clone $this;
        $messages->append($message);

        return $messages;
    }

    /**
     * @return array<int, array{
     *     role: 'system'|'assistant'|'user'|'function',
     *     content: ?string,
     *     function_call?: array{name: string, arguments: string},
     *     name?: string
     * }>
     */
    public function jsonSerialize(): array
    {
        return array_map(
            function (Message $message) {
                $array = [
                    'role' => $message->role->value,
                    'content' => $message->content,
                ];

                if (null !== $message->functionCall) {
                    $array['function_call'] = $message->functionCall;
                }

                if (null !== $message->name) {
                    $array['name'] = $message->name;
                }

                return $array;
            },
            $this->getArrayCopy(),
        );
    }
}
