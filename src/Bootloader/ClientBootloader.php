<?php

declare(strict_types=1);

namespace Spiral\Sentry\Bootloader;

use Sentry\ClientBuilder;
use Sentry\SentrySdk;
use Sentry\State\Hub;
use Sentry\State\HubInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Sentry\Config\SentryConfig;
use Spiral\Sentry\Version;

class ClientBootloader extends Bootloader
{
    protected const SINGLETONS = [
        HubInterface::class => [self::class, 'hub'],
    ];

    public function __construct(
        private readonly ConfiguratorInterface $config
    ) {
    }

    public function init(EnvironmentInterface $env): void
    {
        $this->config->setDefaults('sentry', [
            'dsn' => \trim($env->get('SENTRY_DSN', ''), "\n\t\r \"'") // typical typos
        ]);
    }

    private function hub(SentryConfig $config): HubInterface
    {
        $builder = ClientBuilder::create([
            'dsn' => $config->getDSN()
        ]);

        $builder->setSdkIdentifier(Version::SDK_IDENTIFIER);
        $builder->setSdkVersion(Version::SDK_VERSION);

        $hub = new Hub($builder->getClient());

        SentrySdk::setCurrentHub($hub);

        return $hub;
    }
}
