<?php

declare(strict_types=1);

namespace Spiral\Sentry\Bootloader;

use Spiral\Sentry\SentrySnapshotter;
use Spiral\Snapshots\SnapshotterInterface;

final class SentryBootloader extends AbstractBootloader
{
    protected const BINDINGS = [
        SnapshotterInterface::class => SentrySnapshotter::class
    ];
}
