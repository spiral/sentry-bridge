<?php

declare(strict_types=1);

namespace Spiral\Sentry\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sentry\Options;
use Sentry\State\HubInterface;
use Sentry\State\Scope;

final class SetRequestIpMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly HubInterface $hub,
        private readonly Options $options,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->options->shouldSendDefaultPii()) {
            $this->hub->configureScope(function (Scope $scope) use ($request) {
                $scope->setUser([
                    'ip_address' => $request->getServerParams()['REMOTE_ADDR'] ?? null,
                ]);
            });
        }

        return $handler->handle($request);
    }
}