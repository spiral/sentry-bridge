<?php

declare(strict_types=1);

namespace Spiral\Sentry\Bootloader;

use Sentry\ClientBuilder;
use Sentry\ClientInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Core\Container;
use Spiral\Sentry\Config\SentryConfig;

abstract class AbstractBootloader extends Bootloader
{
    public function __construct(
        private readonly ConfiguratorInterface $config
    ) {
    }

    public function init(EnvironmentInterface $env, Container $container): void
    {
        $container->bindSingleton(ClientInterface::class, [static::class, 'client']);

        $this->config->setDefaults('sentry', [
            'dsn' => trim($env->get('SENTRY_DSN', ''), "\n\t\r \"'") // typical typos
        ]);
    }

    protected function client(SentryConfig $config): ClientInterface
    {
        return ClientBuilder::create([
            'dsn' => $config->getDSN()
        ])->getClient();
    }
}
