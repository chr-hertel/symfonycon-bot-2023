<?php

declare(strict_types=1);

namespace App\SymfonyConBot\ToolBox;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @phpstan-type FunctionParameterDefinition array{
 *     type: 'object',
 *     properties: array<string, array{type: string, description: string}>,
 *     required: list<string>,
 * }
 */
#[AutoconfigureTag('llm_chain.function')]
interface FunctionInterface
{
    public static function getName(): string;

    public static function getDescription(): string;

    /**
     * @return FunctionParameterDefinition
     */
    public static function getParametersDefinition(): array;

    /**
     * @param array<string, mixed> $arguments
     */
    public function execute(array $arguments): string;
}
