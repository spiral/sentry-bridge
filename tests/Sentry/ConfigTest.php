<?php

declare(strict_types=1);

namespace Spiral\Tests\Sentry;

use PHPUnit\Framework\TestCase;
use Spiral\Sentry\Config\SentryConfig;

final class ConfigTest extends TestCase
{
    public function testConfig(): void
    {
        $cfg = new SentryConfig(['dsn' => 'value']);
        $this->assertSame('value', $cfg->getDSN());
    }

    public function testEnvironment(): void
    {
        $cfg = new SentryConfig();
        $this->assertNull($cfg->getEnvironment());

        $cfg = new SentryConfig(['environment' => 'value']);
        $this->assertSame('value', $cfg->getEnvironment());
    }

    public function testRelease(): void
    {
        $cfg = new SentryConfig();
        $this->assertNull($cfg->getRelease());

        $cfg = new SentryConfig(['release' => 'value']);
        $this->assertSame('value', $cfg->getRelease());
    }

    public function testSampleRate(): void
    {
        $cfg = new SentryConfig();
        $this->assertSame(1.0, $cfg->getSampleRate());

        $cfg = new SentryConfig(['sample_rate' => 0.5]);
        $this->assertSame(0.5, $cfg->getSampleRate());
    }

    public function testTracesSampleRate(): void
    {
        $cfg = new SentryConfig();
        $this->assertNull($cfg->getTracesSampleRate());

        $cfg = new SentryConfig(['traces_sample_rate' => 0.5]);
        $this->assertSame(0.5, $cfg->getTracesSampleRate());
    }

    public function testSendDefaultPii(): void
    {
        $cfg = new SentryConfig();
        $this->assertFalse($cfg->isSendDefaultPii());

        $cfg = new SentryConfig(['send_default_pii' => true]);
        $this->assertTrue($cfg->isSendDefaultPii());
    }
}
