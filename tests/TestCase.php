<?php

namespace Spiral\Tests;

use Spiral\Sentry\Bootloader\SentryBootloader;

class TestCase extends \Spiral\Testing\TestCase
{
    public function defineBootloaders(): array
    {
        return [
            SentryBootloader::class,
        ];
    }
}