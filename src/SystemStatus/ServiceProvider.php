<?php
/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\SystemStatus
 */

namespace Translationmanager\SystemStatus;

use Pimple\Container;
use Translationmanager\Service\BootstrappableServiceProvider;

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\SystemStatus
 */
class ServiceProvider implements BootstrappableServiceProvider {
	/**
	 * @inheritdoc
	 */
	public function register( Container $container ) {

		$container['SystemStatus.Controller'] = function () {

			return new Controller();
		};
	}

	/**
	 * @inheritdoc
	 */
	public function boot( Container $container ) {

		( new \Inpsyde\SystemStatus\Assets\Styles(
			$container['translationmanager.plugin']->url( '/assets/css/' ), '' )
		)->init();
	}
}
