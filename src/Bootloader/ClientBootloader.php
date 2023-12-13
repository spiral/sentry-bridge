<?php

declare(strict_types=1);

namespace Spiral\Sentry\Bootloader;

use Sentry\ClientBuilder;
use Sentry\ClientInterface;
use Sentry\Integration\RequestFetcherInterface;
use Sentry\Options;
use Sentry\SentrySdk;
use Sentry\State\Hub;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\DirectoriesInterface;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Boot\FinalizerInterface;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Sentry\Config\SentryConfig;
use Spiral\Sentry\Http\RequestScope;
use Spiral\Sentry\Version;
use Sentry\Integration as SdkIntegration;

class ClientBootloader extends Bootloader
{
    public function __construct(
        private readonly ConfiguratorInterface $config,
    ) {
    }

    public function defineSingletons(): array
    {
        return [
            Options::class => [self::class, 'createOptions'],
            HubInterface::class => [self::class, 'createHub'],
            ClientInterface::class => [self::class, 'getClient'],
            RequestFetcherInterface::class => RequestScope::class,
        ];
    }

    public function init(EnvironmentInterface $env, FinalizerInterface $finalizer): void
    {
        $this->config->setDefaults('sentry', [
            'dsn' => \trim($env->get('SENTRY_DSN', ''), "\n\t\r \"'"), // typical typos
            'environment' => $env->get('SENTRY_ENVIRONMENT') ?? null,
            'release' => $env->get('SENTRY_RELEASE') ?? null,
            'sample_rate' => $env->get('SENTRY_SAMPLE_RATE') === null
                ? 1.0
                : (float)$env->get('SENTRY_SAMPLE_RATE'),
            'traces_sample_rate' => $env->get('SENTRY_PROFILES_SAMPLE_RATE') === null
                ? null
                : (float)$env->get('SENTRY_PROFILES_SAMPLE_RATE'),
            'send_default_pii' => (bool)$env->get('SENTRY_SEND_DEFAULT_PII'),
        ]);
    }

    private function createOptions(
        SentryConfig $config,
        DirectoriesInterface $dirs,
        EnvironmentInterface $env,
        RequestFetcherInterface $requestScope,
    ): Options {
        $options = new Options([
            'dsn' => $config->getDSN(),
            'environment' => $config->getEnvironment(),
            'release' => $config->getRelease(),
        ]);

        $options->setSampleRate($config->getSampleRate());
        $options->setTracesSampleRate($config->getTracesSampleRate());
        $options->setSendDefaultPii($config->isSendDefaultPii());

        $options->setPrefixes([
            $dirs->get('root'),
        ]);

        $options->setInAppExcludedPaths([
            $dirs->get('root') . '/vendor',
        ]);

        if ($config->getEnvironment() === null) {
            $options->setEnvironment($env->get('APP_ENV'));
        }

        if ($config->getRelease() === null) {
            $options->setRelease($env->get('APP_VERSION'));
        }

        $options->setIntegrations(function (array $integrations) use ($options, $requestScope): array {
            if ($options->hasDefaultIntegrations()) {
                // Remove the default error and fatal exception listeners to let Spiral handle those
                // itself. These event are still bubbling up through the documented changes in the users
                // `ExceptionHandler` of their application or through the log channel integration to Sentry
                $integrations = \array_filter(
                    $integrations,
                    static function (SdkIntegration\IntegrationInterface $integration): bool {
                        if ($integration instanceof SdkIntegration\ErrorListenerIntegration) {
                            return false;
                        }

                        if ($integration instanceof SdkIntegration\ExceptionListenerIntegration) {
                            return false;
                        }

                        if ($integration instanceof SdkIntegration\FatalErrorListenerIntegration) {
                            return false;
                        }

                        // We also remove the default request integration so it can be readded
                        // after with a Laravel specific request fetcher. This way we can resolve
                        // the request from Laravel instead of constructing it from the global state
                        if ($integration instanceof SdkIntegration\RequestIntegration) {
                            return false;
                        }

                        return true;
                    },
                );

                $integrations[] = new SdkIntegration\RequestIntegration($requestScope);
            }

            return $integrations;
        });

        return $options;
    }

    private function createHub(Options $options, FinalizerInterface $finalizer): HubInterface
    {
        /**
         * @psalm-suppress InternalClass
         * @psalm-suppress InternalMethod
         */
        $builder = new ClientBuilder($options);

        /** @psalm-suppress InternalMethod */
        $builder->setSdkIdentifier(Version::SDK_IDENTIFIER);

        /** @psalm-suppress InternalMethod */
        $builder->setSdkVersion(Version::SDK_VERSION);

        /** @psalm-suppress InternalMethod */
        $hub = new Hub($builder->getClient());

        SentrySdk::setCurrentHub($hub);

        $finalizer->finalize(static function () use ($hub): void {
            $hub->configureScope(function (Scope $scope): void {
                $scope->removeUser();
            });
        });

        return $hub;
    }

    private function getClient(HubInterface $hub): ClientInterface
    {
        return $hub->getClient();
    }
}
