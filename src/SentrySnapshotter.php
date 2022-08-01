<?php

declare(strict_types=1);

namespace Spiral\Sentry;

use Spiral\Snapshots\Snapshot;
use Spiral\Snapshots\SnapshotInterface;
use Spiral\Snapshots\SnapshotterInterface;

final class SentrySnapshotter implements SnapshotterInterface
{
    public function __construct(
        private readonly Client $client
    ) {
    }

    public function register(\Throwable $exception): SnapshotInterface
    {
        $eventId = $this->client->send($exception);

        $snapshot = new Snapshot(
            $eventId ? (string) $eventId : $this->getID($exception),
            $exception
        );

        return $snapshot;
    }

    protected function getID(\Throwable $exception): string
    {
        return \md5(\implode('|', [$exception->getMessage(), $exception->getFile(), $exception->getLine()]));
    }
}
