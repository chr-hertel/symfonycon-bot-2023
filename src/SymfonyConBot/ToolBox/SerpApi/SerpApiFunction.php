<?php

declare(strict_types=1);

namespace App\SymfonyConBot\ToolBox\SerpApi;

use App\SymfonyConBot\ToolBox\FunctionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @phpstan-import-type FunctionParameterDefinition from FunctionInterface
 */
final readonly class SerpApiFunction implements FunctionInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $apiKey,
    ) {
    }

    public static function getName(): string
    {
        return 'serpapi';
    }

    public static function getDescription(): string
    {
        return 'search for information on the internet';
    }

    /**
     * @return FunctionParameterDefinition
     */
    public static function getParametersDefinition(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => [
                    'type' => 'string',
                    'description' => 'The search query to use',
                ],
            ],
            'required' => ['query'],
        ];
    }

    /**
     * @param array{query: string} $arguments
     */
    public function execute(array $arguments): string
    {
        $response = $this->httpClient->request('GET', 'https://serpapi.com/search', [
            'query' => [
                'q' => $arguments['query'],
                'api_key' => $this->apiKey,
            ],
        ]);

        return sprintf('Results for "%s" are "%s".', $arguments['query'], $this->extractBestResponse($response->toArray()));
    }

    /**
     * @param array<string, mixed> $results
     */
    private function extractBestResponse(array $results): string
    {
        return implode(PHP_EOL, array_map(fn ($story) => sprintf('%s: %s', $story['title'], $story['snippet']), $results['organic_results']));
    }
}
