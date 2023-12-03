<?php

declare(strict_types=1);

namespace App\SymfonyConBot\OpenAI;

use App\SymfonyConBot\Message\Message;
use App\SymfonyConBot\Message\MessageBag;
use App\SymfonyConBot\ToolBox\FunctionRegistry;

final readonly class FunctionChain
{
    public function __construct(
        private ChatModel $model,
        private FunctionRegistry $functionRegistry,
    ) {
    }

    public function call(MessageBag $messages): string
    {
        $response = $this->model->call($messages, [
            'functions' => $this->functionRegistry->getMap(),
        ]);

        while ('function_call' === $response['choices'][0]['finish_reason']) {
            ['name' => $name, 'arguments' => $arguments] = $response['choices'][0]['message']['function_call'];
            $result = $this->functionRegistry->execute($name, $arguments);

            $messages[] = Message::ofAssistant(functionCall: [
                'name' => $name,
                'arguments' => $arguments,
            ]);
            $messages[] = Message::ofFunctionCall($name, $result);

            $response = $this->model->call($messages);
        }

        return $response['choices'][0]['message']['content'];
    }
}
