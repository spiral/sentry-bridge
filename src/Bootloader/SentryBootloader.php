<?php

declare(strict_types=1);

namespace Spiral\Sentry\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Sentry\SentrySnapshotter;
use Spiral\Snapshots\SnapshotterInterface;

final class SentryBootloader extends Bootloader
{
    public function defineDependencies(): array
    {
        return [
            ClientBootloader::class,
        ];
    }

    public function defineBindings(): array
    {
        return [
            SnapshotterInterface::class => SentrySnapshotter::class,
        ];
    }
}
