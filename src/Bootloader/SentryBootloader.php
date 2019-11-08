<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Sentry\Bootloader;

use Sentry\ClientBuilder;
use Sentry\ClientInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Sentry\Config\SentryConfig;
use Spiral\Sentry\SentrySnapshotter;
use Spiral\Snapshots\SnapshotterInterface;

final class SentryBootloader extends Bootloader
{
    protected const SINGLETONS = [
        ClientInterface::class => [self::class, 'client'],
    ];

    protected const BINDINGS = [
        SnapshotterInterface::class => SentrySnapshotter::class
    ];

    /** @var ConfiguratorInterface */
    private $config;

    /**
     * @param ConfiguratorInterface $config
     */
    public function __construct(ConfiguratorInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param EnvironmentInterface $env
     */
    public function boot(EnvironmentInterface $env): void
    {
        $this->config->setDefaults('sentry', [
            'dsn' => trim($env->get('SENTRY_DSN', ''), "\n\t\r \"'") // typical typos
        ]);
    }

    /**
     * @param SentryConfig $config
     * @return ClientInterface
     */
    private function client(SentryConfig $config): ClientInterface
    {
        return ClientBuilder::create([
            'dsn' => $config->getDSN()
        ])->getClient();
    }
}
