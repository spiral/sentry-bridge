<?php

declare(strict_types=1);

namespace Spiral\Sentry\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Exceptions\ExceptionHandler;
use Spiral\Sentry\SentryReporter;

final class SentryReporterBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        ClientBootloader::class,
    ];

    public function boot(ExceptionHandler $exceptionHandler, SentryReporter $reporter): void
    {
        $exceptionHandler->addReporter($reporter);
    }
}
