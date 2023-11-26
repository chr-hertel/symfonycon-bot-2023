<?php

declare(strict_types=1);

namespace App\SymfonyConBot;

use App\Entity\Event;
use App\SymfonyConBot\Message\Message;
use App\SymfonyConBot\Message\MessageBag;
use App\SymfonyConBot\Message\MessageStore;
use App\SymfonyConBot\OpenAI\ChatModel;
use App\SymfonyConBot\OpenAI\Embeddings;
use App\SymfonyConBot\Schedule\VectorStore;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ChatBot
{
    private const SYSTEM_PROMPT = <<<PROMPT
        You are a chat bot helping users to navigate the SymfonyCon Brussels.
        You can answer questions about the topics of the conference, the speakers and the schedule.
        You write your answers in the language of the user's language code.
        PROMPT;

    private const ASSISTANT_INTRO = <<<PROMPT
        The SymfonyCon Brussels 2023 is a 4-day event from December 5th to December 8th. The conference is dedicated to Symfony and PHP.
        The venue Square conference center situated on the imposing Mont des Arts with a broad vista over Brussels.
        The first two days, December 5th and 6th, are dedicated to workshops. The conference days are December 7th and 8th.
        There is a community hackday on December 9th hosted by Smile Benelux at Avenue de Broqueville 12, 1150 Brussels.
        PROMPT;

    public function __construct(
        private MessageStore $messageStore,
        private Embeddings $embeddings,
        private VectorStore $vectorStore,
        private EntityManagerInterface $entityManager,
        private ChatModel $model,
    ) {
    }

    public function message(int $userId, string $content): string
    {
        $bag = $this->messageStore->load($userId);

        if (0 === count($bag)) {
            $bag[] = Message::forSystem(self::SYSTEM_PROMPT);
            $bag[] = Message::ofAssistant(self::ASSISTANT_INTRO);
        }

        $message = Message::ofUser($content);
        $retrieval = $this->retrievalMessage($message);
        $response = $this->call($retrieval, $bag);

        $bag[] = $message;
        $bag[] = Message::ofAssistant($response);

        $this->messageStore->save($bag, $userId);

        return $response;
    }

    private function retrievalMessage(Message $message): Message
    {
        $vector = $this->embeddings->create($message->content ?? '');
        $ids = $this->vectorStore->query($vector);

        $prompt = <<<PROMPT
            Answer to the question at the end based only on the information in this message or previous assistant messages.
            Do not add information and if you do not find an answer, say so.
            PROMPT;
        foreach ($ids as $id) {
            // single query due to some sqlite thingy
            $event = $this->entityManager->find(Event::class, $id);

            if (null === $event) {
                continue;
            }

            $prompt .= $event->toString().PHP_EOL;
        }

        $prompt .= '. Question: '.$message->content;

        return Message::ofUser($prompt);
    }

    private function call(Message $message, MessageBag $messages): string
    {
        $response = $this->model->call($messages->with($message));

        return $response['choices'][0]['message']['content'];
    }
}
