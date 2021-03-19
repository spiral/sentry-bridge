<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Sentry;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Sentry\Breadcrumb;
use Sentry\ClientInterface;
use Sentry\State\Scope;
use Spiral\Debug\StateInterface;
use Spiral\Logger\Event\LogEvent;
use Spiral\Snapshots\Snapshot;
use Spiral\Snapshots\SnapshotInterface;
use Spiral\Snapshots\SnapshotterInterface;

final class SentrySnapshotter implements SnapshotterInterface
{
    /** @var ClientInterface */
    private $client;

    /** @var StateInterface|null */
    private $state;

    /** @var LoggerInterface|null */
    private $logger;

    /**
     * @param ClientInterface      $client
     * @param StateInterface|null  $state
     * @param LoggerInterface|null $logger
     */
    public function __construct(ClientInterface $client, StateInterface $state = null, LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->state = $state;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function register(\Throwable $e): SnapshotInterface
    {
        $scope = new Scope();

        if (null !== $this->state) {
            $scope->setTags($this->state->getTags());
            $scope->setExtras($this->state->getVariables());

            foreach ($this->state->getLogEvents() as $event) {
                $scope->addBreadcrumb($this->makeBreadcrump($event));
            }
        }

        $eventId = $this->client->captureException($e, $scope);
        $snapshot = new Snapshot(
            $eventId ? (string) $eventId : $this->getID($e),
            $e
        );

        if (null !== $this->logger) {
            $this->logger->error($snapshot->getMessage());
        }

        return $snapshot;
    }

    /**
     * @param \Throwable $e
     * @return string
     */
    protected function getID(\Throwable $e): string
    {
        return md5(join('|', [$e->getMessage(), $e->getFile(), $e->getLine()]));
    }

    /**
     * @param LogEvent $event
     * @return Breadcrumb
     */
    private function makeBreadcrump(LogEvent $event): Breadcrumb
    {
        $level = '';
        switch ($event->getLevel()) {
            case LogLevel::CRITICAL:
            case LogLevel::EMERGENCY:
            case LogLevel::ALERT:
                $level = Breadcrumb::LEVEL_FATAL;
                break;
            case LogLevel::ERROR:
                $level = Breadcrumb::LEVEL_ERROR;
                break;
            case LogLevel::INFO:
                $level = Breadcrumb::LEVEL_INFO;
                break;
            case LogLevel::DEBUG:
                $level = Breadcrumb::LEVEL_DEBUG;
                break;
            case LogLevel::WARNING:
            case LogLevel::NOTICE:
                $level = Breadcrumb::LEVEL_WARNING;
                break;
        }

        return new Breadcrumb(
            $level,
            'default',
            $event->getChannel(),
            $event->getMessage(),
            $event->getContext()
        );
    }
}
