<?php

declare(strict_types=1);

namespace App\SymfonyConBot\ToolBox;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

/**
 * @phpstan-import-type FunctionParameterDefinition from FunctionInterface
 */
final readonly class FunctionRegistry
{
    /**
     * @var array<string, FunctionInterface>
     */
    private array $functions;

    /**
     * @param iterable<FunctionInterface> $functions
     */
    public function __construct(
        #[TaggedIterator('llm_chain.function', defaultIndexMethod: 'getName')]
        iterable $functions,
    ) {
        $this->functions = $functions instanceof \Traversable ? iterator_to_array($functions) : $functions;
    }

    /**
     * @return list<array{
     *     name: string,
     *     description: string,
     *     parameters: FunctionParameterDefinition,
     * }>
     */
    public function getMap(): array
    {
        $functionsMap = [];

        foreach ($this->functions as $function) {
            $functionsMap[] = [
                'name' => $function->getName(),
                'description' => $function->getDescription(),
                'parameters' => $function->getParametersDefinition(),
            ];
        }

        return $functionsMap;
    }

    public function execute(string $name, string $arguments): string
    {
        return $this->functions[$name]->execute(json_decode($arguments, true));
    }
}
