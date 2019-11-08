<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Tests\Sentry;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;
use Sentry\ClientInterface;
use Spiral\Debug\State;
use Spiral\Logger\Event\LogEvent;
use Spiral\Sentry\SentrySnapshotter;

class SnapshotterTest extends TestCase
{
    public function testLogger(): void
    {
        $logger = new TestLogger();

        $client = m::mock(ClientInterface::class);
        $client->expects('captureException');

        $sentry = new SentrySnapshotter(
            $client,
            null,
            $logger
        );

        $sentry->register(new \Error('hello world'));
        $this->assertTrue($logger->hasErrorRecords());
    }

    public function testLoggerWithState(): void
    {
        $logger = new TestLogger();

        $client = m::mock(ClientInterface::class);
        $client->expects('captureException');

        $state = new State();
        $state->setTag('test', 'tag');
        $state->addLogEvent(new LogEvent(new \DateTime(), 'default', 'error', 'hello world'));

        $sentry = new SentrySnapshotter(
            $client,
            $state,
            $logger
        );

        $sentry->register(new \Error('hello world'));
        $this->assertTrue($logger->hasErrorRecords());
    }
}
