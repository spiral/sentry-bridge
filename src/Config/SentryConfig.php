<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Sentry\Config;

use Spiral\Core\InjectableConfig;

/**
 * Configures sentry extension.
 */
final class SentryConfig extends InjectableConfig
{
    public const CONFIG = 'sentry';

    /** @var array */
    protected $config = [
        'dsn' => '',
    ];

    /**
     * @return string
     */
    public function getDSN(): string
    {
        return $this->config['dsn'];
    }
}
