<?php

declare(strict_types=1);

namespace Spiral\Sentry\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sentry\Integration\RequestFetcherInterface;

final class RequestScope implements RequestFetcherInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    public function fetchRequest(): ?ServerRequestInterface
    {
        if (!$this->container->has(ServerRequestInterface::class)) {
            return null;
        }

        return $this->container->get(ServerRequestInterface::class);
    }
}