<?php

declare(strict_types=1);

namespace App\Command;

use App\SymfonyConBot\OpenAI\Embeddings;
use App\SymfonyConBot\Schedule\Crawler;
use App\SymfonyConBot\Schedule\VectorStore;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:schedule:crawl', description: 'Load conference schedule from live.symfony.com')]
final class ScheduleCrawlerCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly VectorStore $vectorStore,
        private readonly Crawler $crawler,
        private readonly Embeddings $embeddings,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Crawling latest schedule information from live.symfony.com');

        if (!$io->confirm('Really want to replace the current schedule?', false)) {
            return 0;
        }

        $this->truncateSlots();
        $this->loadSlots();

        return 0;
    }

    private function truncateSlots(): void
    {
        // Vector Store
        $this->vectorStore->truncate();

        // Database
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();

        $connection->executeStatement($platform->getTruncateTableSQL('slot', true));
    }

    private function loadSlots(): void
    {
        $embeddings = [];
        foreach ($this->crawler->loadSchedule() as $slot) {
            foreach ($slot->getEvents() as $event) {
                $embeddings[] = [
                    'id' => $event->getId(),
                    'values' => $this->embeddings->create($event->toString()),
                ];
            }

            $this->entityManager->persist($slot);
        }
        $this->vectorStore->upsert($embeddings);
        $this->entityManager->flush();
    }
}
