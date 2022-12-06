<?php

namespace Spiral\Tests\Sentry;

use Sentry\ClientInterface;
use Sentry\Client;
use Spiral\Tests\TestCase;

final class ClientBootloaderTest extends TestCase
{
    public function testClientBound(): void
    {
        $this->assertContainerBoundAsSingleton(ClientInterface::class, Client::class);
    }
}