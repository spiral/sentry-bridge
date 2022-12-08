<?php

namespace Spiral\Tests\Sentry;

use Spiral\Sentry\SentrySnapshotter;
use Spiral\Snapshots\SnapshotterInterface;
use Spiral\Tests\TestCase;

final class SentryBootloaderTest extends TestCase
{
    public function testSnapshotterBound(): void
    {
        $this->assertContainerBound(SnapshotterInterface::class, SentrySnapshotter::class);
    }
}