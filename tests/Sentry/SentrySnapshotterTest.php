<?php

declare(strict_types=1);

namespace Spiral\Tests\Sentry;

use Mockery as m;
use Sentry\State\HubInterface;
use Spiral\Core\Container;
use Spiral\Core\Scope;
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
        $hub = m::mock(HubInterface::class);

        $hub->expects('configureScope');
        $hub->expects('captureException');

        $container = new Container();
        $container->bindSingleton(StateInterface::class, new State());

        $sentry = new SentrySnapshotter(new Client($hub, $container));

        $this->assertInstanceOf(SnapshotInterface::class, $sentry->register(new \Error('hello world')));
    }

    public function testScopedState(): void
    {
        // DTOs
        $scope = new \Sentry\State\Scope();
        $scopedState = new State();
        $scopedState->setTag('foo', 'bar');
        $rootState = new State();
        $rootState->setTag('root', 'baz');
        // Mock Hub
        $hub = $this->createMock(HubInterface::class);
        $hub->expects($this->once())
            ->method('configureScope')
            ->with($this->callback(function (callable $modifier) use ($scope) {
                $modifier($scope);
                return true;
            }));
        $hub->expects($this->once())->method('captureException');
        // Create container
        $container = new Container();
        $container->bindSingleton(StateInterface::class, $rootState);
        $container->bindSingleton(HubInterface::class, $hub);

        $container->runScope(
            new Scope(name: 'test', bindings: [
                StateInterface::class => $scopedState,
            ]),
            function (SentrySnapshotter $snapshotter) {
                $snapshotter->register(new \Error('hello world'));
            }
        );

        $tags = (fn() => $this->tags)->call($scope);
        self::assertNotSame(['root' => 'baz'], $tags, 'Root state should not be used');
        self::assertSame(['foo' => 'bar'], $tags);
    }
}
