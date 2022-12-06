<?php

namespace Spiral\Tests\Sentry;

use Spiral\Tests\TestCase;
use Sentry\ClientInterface;
use Sentry\EventId;
use Spiral\Core\Container;
use Spiral\Debug\State;
use Spiral\Debug\StateInterface;
use Spiral\Sentry\Client;
use Mockery as m;

final class ClientTest extends TestCase
{
    public function testSend(): void
    {
        $container = new Container();
        $container->bindSingleton(StateInterface::class, new State());

        $mainClient = new Client(
            $client = m::mock(ClientInterface::class),
            $container
        );

        $errorException = new \ErrorException('Test exception');

        $client->shouldReceive('captureException')
            ->once()
            ->withArgs(function (\Throwable $exception) use ($errorException) {
                return $errorException === $exception;
            })
            ->andReturn(
                $eventId = new EventId('c8c46e00bf53942206fd2ad9546daac2')
            );

        $this->assertSame($eventId, $mainClient->send($errorException));
    }
}