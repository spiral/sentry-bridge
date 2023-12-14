<?php

declare(strict_types=1);

namespace Spiral\Sentry;

use Spiral\Snapshots\Snapshot;
use Spiral\Snapshots\SnapshotInterface;
use Spiral\Snapshots\SnapshotterInterface;

final class SentrySnapshotter implements SnapshotterInterface
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    public function register(\Throwable $e): SnapshotInterface
    {
        $eventId = $this->client->send($e);

        return new Snapshot(
            $eventId ? (string)$eventId : $this->getID($e),
            $e,
        );
    }

    protected function getID(\Throwable $e): string
    {
        return \md5(\implode('|', [$e->getMessage(), $e->getFile(), $e->getLine()]));
    }
}
