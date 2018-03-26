<?php
/**
 * Interface IntegrableServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Service
 */

namespace Translationmanager\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Interface IntegrableServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Service
 */
interface IntegrableServiceProvider extends ServiceProviderInterface {

	/**
	 * Integrate service into plugin
	 *
	 * @since 1.0.0
	 *
	 * @param \Pimple\Container $container The container instance.
	 *
	 * @return void
	 */
	public function integrate( Container $container );
}
