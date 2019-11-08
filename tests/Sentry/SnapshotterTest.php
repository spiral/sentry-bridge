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
use Spiral\Sentry\SentrySnapshotter;

class SnapshotterTest extends TestCase
{
    public function testLogger()
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

    public function testLoggerWithState()
    {
        $logger = new TestLogger();

        $client = m::mock(ClientInterface::class);
        $client->expects('captureException');

        $state = new State();
        $state->setTag('test', 'tag');

        $sentry = new SentrySnapshotter(
            $client,
            $state,
            $logger
        );

        $sentry->register(new \Error('hello world'));
        $this->assertTrue($logger->hasErrorRecords());
    }

}
