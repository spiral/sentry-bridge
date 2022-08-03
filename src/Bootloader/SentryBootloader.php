<?php

declare(strict_types=1);

namespace Spiral\Sentry\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Sentry\SentrySnapshotter;
use Spiral\Snapshots\SnapshotterInterface;

final class SentryBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        ClientBootloader::class,
    ];

    protected const BINDINGS = [
        SnapshotterInterface::class => SentrySnapshotter::class,
    ];
}
