<?php
/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Module
 */

namespace Translationmanager\Module;

use Pimple\Container;
use Translationmanager\Service\IntegrableServiceProvider;

/**
 * Class ServiceProvider
 *
 * @since   1.0.0
 * @package Translationmanager\Module
 */
class ServiceProvider implements IntegrableServiceProvider {
	/**
	 * @inheritdoc
	 */
	public function register( Container $container ) {

		$container[ Loader::class ] = function ( Container $container ) {

			$plugins = get_option( 'active_plugins', [] );

			if ( function_exists( 'wp_get_active_network_plugins' ) ) {
				$plugins = array_merge( $plugins, wp_get_active_network_plugins() );
			}

			return new Loader( $container['translationmanager.plugin'], $plugins );
		};
	}

	/**
	 * @inheritdoc
	 */
	public function integrate( Container $container ) {

		$container[ Loader::class ]
			->register_integrations()
			->integrate();
	}
}
