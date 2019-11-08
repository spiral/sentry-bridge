<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Tests\Sentry;

use PHPUnit\Framework\TestCase;
use Spiral\Sentry\Config\SentryConfig;

class ConfigTest extends TestCase
{
    public function testConfig(): void
    {
        $cfg = new SentryConfig(['dsn' => 'value']);
        $this->assertSame('value', $cfg->getDSN());
    }
}
