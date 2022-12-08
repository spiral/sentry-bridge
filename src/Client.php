<?php

declare(strict_types=1);

namespace Spiral\Sentry;

use Psr\Container\ContainerInterface;
use Psr\Log\LogLevel;
use Sentry\Breadcrumb;
use Sentry\ClientInterface;
use Sentry\EventId;
use Sentry\State\Scope;
use Spiral\Debug\StateInterface;
use Spiral\Logger\Event\LogEvent;

final class Client
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly ContainerInterface $container,
    ) {
    }

    public function send(\Throwable $exception): ?EventId
    {
        $state = $this->container->get(StateInterface::class);

        $scope = new Scope();

        if ($state !== null) {
            $scope->setTags($state->getTags());
            $scope->setExtras($state->getVariables());

            foreach ($state->getLogEvents() as $event) {
                $scope->addBreadcrumb($this->makeBreadcrumb($event));
            }
        }

        return $this->client->captureException($exception, $scope);
    }

    private function makeBreadcrumb(LogEvent $event): Breadcrumb
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
