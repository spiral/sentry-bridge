<?php

declare(strict_types=1);

namespace Spiral\Exceptions\Reporter;

use Spiral\Exceptions\ExceptionReporterInterface;
use Spiral\Sentry\Client;

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
