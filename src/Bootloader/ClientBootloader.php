<?php

declare(strict_types=1);

namespace Spiral\Sentry\Bootloader;

use Sentry\ClientBuilder;
use Sentry\ClientInterface;
use Sentry\SentrySdk;
use Sentry\State\Hub;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Sentry\Config\SentryConfig;
use Spiral\Sentry\Version;

class ClientBootloader extends Bootloader
{
    protected const SINGLETONS = [
        ClientInterface::class => [self::class, 'createClient'],
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

    private function createClient(SentryConfig $config): ClientInterface
    {
        $builder = ClientBuilder::create([
            'dsn' => $config->getDSN(),
        ]);

        $builder->setSdkIdentifier(Version::SDK_IDENTIFIER);
        $builder->setSdkVersion(Version::SDK_VERSION);

        $client = $builder->getClient();

        SentrySdk::setCurrentHub(new Hub($client));

        return $client;
    }
}
