<?php

declare(strict_types=1);

namespace Spiral\Tests\Sentry;

use Mockery as m;
use Psr\Container\ContainerInterface;
use Sentry\ClientInterface;
use Spiral\Core\Container;
use Spiral\Debug\State;
use Spiral\Debug\StateInterface;
use Spiral\Sentry\Client;
use Spiral\Sentry\SentrySnapshotter;
use Spiral\Snapshots\SnapshotInterface;
use Spiral\Tests\TestCase;

final class SentrySnapshotterTest extends TestCase
{
    public function testRegister(): void
    {
        $client = m::mock(ClientInterface::class);
        $client->expects('captureException');

        $container = new Container();
        $container->bindSingleton(StateInterface::class, new State());

        $sentry = new SentrySnapshotter(new Client($client, $container));

        $this->assertInstanceOf(SnapshotInterface::class, $sentry->register(new \Error('hello world')));
    }
}
