<?php

declare(strict_types=1);

namespace Spiral\Sentry;

use Spiral\Exceptions\ExceptionReporterInterface;

final class SentryReporter implements ExceptionReporterInterface
{
    public function __construct(
        private readonly Client $client
    ) {
    }

    public function report(\Throwable $exception): void
    {
        $this->client->send($exception);
    }
}
