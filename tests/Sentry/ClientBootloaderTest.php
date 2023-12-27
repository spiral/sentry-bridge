<?php

namespace Spiral\Tests\Sentry;

use Sentry\ClientInterface;
use Sentry\Client;
use Sentry\Integration\RequestFetcherInterface;
use Sentry\Options;
use Sentry\State\Hub;
use Sentry\State\HubInterface;
use Spiral\Sentry\Config\SentryConfig;
use Spiral\Sentry\Http\RequestScope;
use Spiral\Testing\Attribute\Env;
use Spiral\Tests\TestCase;

final class ClientBootloaderTest extends TestCase
{
    public function testClientBound(): void
    {
        $this->assertContainerBoundAsSingleton(ClientInterface::class, Client::class);
    }

    public function testHubBound(): void
    {
        $this->assertContainerBoundAsSingleton(HubInterface::class, Hub::class);
    }

    public function testOptionsBound(): void
    {
        $this->assertContainerBoundAsSingleton(Options::class, Options::class);
    }

    public function testRequestScopeBound(): void
    {
        $this->assertContainerBoundAsSingleton(RequestFetcherInterface::class, RequestScope::class);
    }

    public function testDefaultConfig(): void
    {
        $config = $this->getConfig(SentryConfig::CONFIG);

        $this->assertSame('', $config['dsn']);
        $this->assertNull($config['environment']);
        $this->assertNull($config['release']);
        $this->assertSame(1.0, $config['sample_rate']);
        $this->assertSame(null, $config['traces_sample_rate']);
        $this->assertFalse($config['send_default_pii']);
    }

    #[Env('SENTRY_DSN', 'http://example.com')]
    #[Env('SENTRY_ENVIRONMENT', 'testing')]
    #[Env('SENTRY_RELEASE', '1.0.1')]
    #[Env('SENTRY_SAMPLE_RATE', '0.5')]
    #[Env('SENTRY_TRACES_SAMPLE_RATE', '0.7')]
    #[Env('SENTRY_SEND_DEFAULT_PII', 'true')]
    public function testConfigWithEnv(): void
    {
        $config = $this->getConfig(SentryConfig::CONFIG);

        $this->assertSame('http://example.com', $config['dsn']);
        $this->assertSame('testing', $config['environment']);
        $this->assertSame('1.0.1', $config['release']);
        $this->assertSame(0.5, $config['sample_rate']);
        $this->assertSame(0.7, $config['traces_sample_rate']);
        $this->assertTrue($config['send_default_pii']);
    }

    #[Env('APP_ENV', 'foo')]
    public function testDetectEnvironmentFromAppEnv(): void
    {
        $options = $this->getContainer()->get(Options::class);
        $this->assertSame('foo', $options->getEnvironment());
    }

    #[Env('SENTRY_ENVIRONMENT', 'bar')]
    public function testDetectEnvironmentFromSentryEnv(): void
    {
        $options = $this->getContainer()->get(Options::class);
        $this->assertSame('bar', $options->getEnvironment());
    }

    public function testDefaultEnvironment(): void
    {
        $options = $this->getContainer()->get(Options::class);
        $this->assertNull($options->getEnvironment());
    }

    #[Env('APP_VERSION', '1.0.0')]
    public function testDetectReleaseFromAppEnv(): void
    {
        $options = $this->getContainer()->get(Options::class);
        $this->assertSame('1.0.0', $options->getRelease());
    }

    #[Env('SENTRY_RELEASE', '1.0.1')]
    public function testDetectReleaseFromSentryEnv(): void
    {
        $options = $this->getContainer()->get(Options::class);
        $this->assertSame('1.0.1', $options->getRelease());
    }

    public function testDefaultRelease(): void
    {
        $options = $this->getContainer()->get(Options::class);
        $this->assertNull($options->getRelease());
    }
}