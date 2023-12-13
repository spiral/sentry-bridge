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
        'environment' => null,
        'release' => null,
        'sample_rate' => 1.0,
        'traces_sample_rate' => null,
        'send_default_pii' => false,
    ];

    /**
     * @see https://docs.sentry.io/product/sentry-basics/dsn-explainer/
     */
    public function getDSN(): string
    {
        return $this->config['dsn'];
    }

    /**
     * This string is freeform and set to production by default. A release can be associated with
     * more than one environment to separate them in the UI (think staging vs production or similar).
     */
    public function getEnvironment(): ?string
    {
        return $this->config['environment'] ?? $_SERVER['SENTRY_ENVIRONMENT'] ?? null;
    }

    /**
     * The release version of your application.
     * Example with dynamic git hash: trim(exec('git --git-dir ' . base_path('.git') . ' log --pretty="%h" -n1 HEAD'))
     */
    public function getRelease(): ?string
    {
        return $this->config['release'] ?? $_SERVER['SENTRY_RELEASE'] ?? null;
    }

    /**
     * Configures the sample rate for error events, in the range of 0.0 to 1.0. The default is 1.0 which means that
     * 100% of error events are sent. If set to 0.1 only 10% of error events will be sent. Events are picked randomly.
     *
     * @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#sample-rate
     */
    public function getSampleRate(): float
    {
        return $this->config['sample_rate'];
    }

    /**
     * A number between 0 and 1, controlling the percentage chance a given transaction will be sent to Sentry.
     * (0 represents 0% while 1 represents 100%.) Applies equally to all transactions created in the app. Either this
     * or traces_sampler must be defined to enable tracingThe process of logging the events that took place during a
     * request, often across multiple services..
     *
     * @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#traces-sample-rate
     */
    public function getTracesSampleRate(): ?float
    {
        return $this->config['traces_sample_rate'];
    }

    /**
     * If this flag is enabled, certain personally identifiable information (PII) is added by active integrations.
     * By default, no such data is sent.
     *
     * @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#send-default-pii
     */
    public function isSendDefaultPii(): bool
    {
        return $this->config['send_default_pii'] ?? false;
    }
}