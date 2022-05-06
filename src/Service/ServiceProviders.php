<?php

/**
 * Service Providers
 *
 * @since   1.0.0
 * @package Translationmanager\Service
 */

namespace Translationmanager\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Translationmanager\Service\Exception\BootstrappedException;

/**
 * Class ServiceProviders
 *
 * @since   1.0.0
 * @package Translationmanager\Service
 */
final class ServiceProviders
{
    /**
     * Container
     *
     * @since 1.0.0
     *
     * @var \Pimple\Container The instance of the container
     */
    private $container;

    /**
     * Providers
     *
     * @since 1.0.0
     *
     * @var array The providers collection
     */
    private $providers = [];

    /**
     * Bootstrapped
     *
     * @since 1.0.0
     *
     * @var bool True if the providers are been bootstrapped, false otherwise
     */
    private $bootstrapped = false;

    /**
     * ServiceProviders constructor
     *
     * @param \Pimple\Container $container The instance of the container.
     *
     * @since 1.0.0
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Register
     *
     * @param \Pimple\ServiceProviderInterface $provider The provider to register.
     *
     * @return $this For concatenation
     * @since 1.0.0
     */
    public function register(ServiceProviderInterface $provider)
    {
        $this->container->register($provider);
        $this->providers[] = $provider;

        return $this;
    }

    /**
     * Bootstrap Providers
     *
     * @return $this For concatenation
     * @since 1.0.0
     */
    public function bootstrap()
    {
        if ($this->bootstrapped) {
            throw new BootstrappedException('All ready Bootstrapped.');
        }

        $this->bootstrapped = true;

        foreach ($this->providers as $provider) {
            if ($provider instanceof BootstrappableServiceProvider) {
                $provider->boot($this->container);
            }
        }

        return $this;
    }

    /**
     * Integrate Providers
     *
     * @return $this For concatenation
     * @since 1.0.0
     */
    public function integrate()
    {
        foreach ($this->providers as $provider) {
            if ($provider instanceof IntegrableServiceProvider) {
                $provider->integrate($this->container);
            }
        }

        return $this;
    }
}
