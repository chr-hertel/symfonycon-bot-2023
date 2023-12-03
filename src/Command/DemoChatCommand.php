<?php

declare(strict_types=1);

namespace App\Command;

use App\SymfonyConBot\Message\Message;
use App\SymfonyConBot\Message\MessageBag;
use App\SymfonyConBot\OpenAI\ChatModel;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:demo:chat', description: 'Command for testing chat')]
final class DemoChatCommand extends Command
{
    public function __construct(private readonly ChatModel $model)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Demo Chat');

        $prompt = $io->ask('What do you want to know?', 'What is the latest Symfony version?');
        $response = $this->model->call(new MessageBag(Message::ofUser($prompt)));

        $io->block($response['choices'][0]['message']['content']);

        return 0;
    }
}
