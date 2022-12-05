<?php

declare(strict_types=1);

namespace Spiral\Tests\Sentry;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Sentry\ClientInterface;
use Spiral\Sentry\Client;
use Spiral\Sentry\SentrySnapshotter;
use Spiral\Snapshots\SnapshotInterface;

final class SnapshotterTest extends TestCase
{
    public function testRegister(): void
    {
        $client = m::mock(ClientInterface::class);
        $client->expects('captureException');

        $sentry = new SentrySnapshotter(new Client($client, $container = m::mock(ContainerInterface::class)));
        $container->expects('get');

        $this->assertInstanceOf(SnapshotInterface::class, $sentry->register(new \Error('hello world')));
    }
}
