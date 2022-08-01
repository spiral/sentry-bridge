<?php

declare(strict_types=1);

namespace Spiral\Sentry\Config;

use Spiral\Core\InjectableConfig;

/**
 * Configures sentry extension.
 */
final class SentryConfig extends InjectableConfig
{
    public const CONFIG = 'sentry';

    protected array $config = [
        'dsn' => '',
    ];

    public function getDSN(): string
    {
        return $this->config['dsn'];
    }
}
