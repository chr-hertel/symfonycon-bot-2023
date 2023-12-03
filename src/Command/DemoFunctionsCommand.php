<?php

declare(strict_types=1);

namespace App\Command;

use App\SymfonyConBot\Message\Message;
use App\SymfonyConBot\Message\MessageBag;
use App\SymfonyConBot\OpenAI\FunctionChain;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:demo:functions', description: 'Command for testing function calls')]
final class DemoFunctionsCommand extends Command
{
    public function __construct(private readonly FunctionChain $chain)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Demo Functions');

        $prompt = $io->ask('What do you want to know?', 'What is the latest Symfony version?');
        $response = $this->chain->call(new MessageBag(Message::ofUser($prompt)));

        $io->writeln($response);

        return 0;
    }
}
