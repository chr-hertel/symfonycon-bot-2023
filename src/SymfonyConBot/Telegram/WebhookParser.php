<?php

declare(strict_types=1);

namespace App\SymfonyConBot\Telegram;

use App\SymfonyConBot\Telegram\Data\Update;
use Symfony\Component\HttpFoundation\ChainRequestMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\IsJsonRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\MethodRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerException;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Webhook\Client\AbstractRequestParser;
use Symfony\Component\Webhook\Exception\RejectWebhookException;

final class WebhookParser extends AbstractRequestParser
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    protected function getRequestMatcher(): RequestMatcherInterface
    {
        return new ChainRequestMatcher([
            new IsJsonRequestMatcher(),
            new MethodRequestMatcher('POST'),
        ]);
    }

    protected function doParse(Request $request, #[\SensitiveParameter] string $secret): ?RemoteEvent
    {
        try {
            $update = $this->serializer->deserialize($request->getContent(), Update::class, 'json', [
                DateTimeNormalizer::FORMAT_KEY => 'U',
            ]);
        } catch (SerializerException $exception) {
            throw new RejectWebhookException(message: 'Unexpected JSON payload', previous: $exception);
        }

        return new UpdateEvent($update);
    }
}
