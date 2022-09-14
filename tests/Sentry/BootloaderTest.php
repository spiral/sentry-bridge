<?php

declare(strict_types=1);

namespace Spiral\Tests\Sentry;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Sentry\Client;
use Sentry\ClientInterface;
use Spiral\Boot\BootloadManager\BootloadManager;
use Spiral\Boot\BootloadManager\Initializer;
use Spiral\Boot\Environment;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Core\Container;
use Spiral\Sentry\Bootloader\SentryBootloader;
use Spiral\Sentry\Config\SentryConfig;

class BootloaderTest extends TestCase
{
    public function testBootloader(): void
    {
        $config = m::mock(ConfiguratorInterface::class);

        $c = new Container();
        $c->bind(ConfiguratorInterface::class, $config);
        $c->bind(EnvironmentInterface::class, new Environment([
            'SENTRY_DSN' => 'test'
        ]));

        $config->expects('setDefaults')->with('sentry', [
            'dsn' => 'test'
        ]);

        (new BootloadManager($c, $c, $c, new Initializer($c, $c)))->bootload([SentryBootloader::class]);

        $c->bind(SentryConfig::class, new SentryConfig([
            'dsn' => 'https://key@sentry.demo/2'
        ]));

        $client = $c->get(ClientInterface::class);

        $this->assertInstanceOf(Client::class, $client);

        /** @var Client $client */
        $this->assertSame('https://key@sentry.demo/2', (string)$client->getOptions()->getDsn());
    }
}
