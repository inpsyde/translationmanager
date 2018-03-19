<?php
/**
 * Bootstrapper
 *
 * @since   1.0.0
 * @package Translationmanager\Service
 */

namespace Translationmanager\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Translationmanager\Service\Exception\BootstrappedException;

/**
 * Class Bootstrapper
 *
 * @since   1.0.0
 * @package Translationmanager\Service
 */
final class Bootstrapper {

	private $container;

	private $providers = [];

	private $bootstrapped = false;

	public function __construct( Container $container ) {

		$this->container = $container;
	}

	public function register( ServiceProviderInterface $provider ) {

		$this->container->register( $provider );
		$this->providers[] = $provider;

		return $this;
	}

	public function bootstrap() {

		if ( $this->bootstrapped ) {
			throw new BootstrappedException( 'All ready Bootstrapped.' );
		}

		$this->bootstrapped = true;

		foreach ( $this->providers as $provider ) {
			if ( $provider instanceof BootstrappableServiceProvider ) {
				$provider->boot( $this->container );
			}
		}

		return $this;
	}
}
