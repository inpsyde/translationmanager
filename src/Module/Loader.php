<?php
/**
 * Class Loader
 *
 * @since   1.0.0
 * @package Translationmanager\Module
 */

namespace Translationmanager\Module;

use Translationmanager\Module\Mlp;
use Translationmanager\Module\YoastSeo;
use Translationmanager\Plugin;

/**
 * Class Loader
 *
 * @since   1.0.0
 * @package Translationmanager\Module
 */
class Loader {

	/**
	 * Plugin
	 *
	 * @since 1.0.0
	 *
	 * @var \Translationmanager\Plugin Instance of the plugin class
	 */
	private $plugin;

	/**
	 * Installed Plugins List
	 *
	 * @since 1.0.0
	 *
	 * @var array The list of the installed and active plugins
	 */
	private $installed_plugins;

	/**
	 * List of Integrations
	 *
	 * @var array List of integrations
	 */
	private $integrations = [];

	/**
	 * Modules
	 *
	 * @since 1.0.0
	 *
	 * @var array List of modules we want to integrate if the relative plugins are active
	 */
	private static $modules = [
		'multilingualpress'  => Mlp\Integrate::class,
		'multilingual-press' => Mlp\Integrate::class,
		'wp-seo'             => YoastSeo\Integrate::class,
	];

	/**
	 * Loader constructor
	 *
	 * @param \Translationmanager\Plugin $plugin            Instance of the plugin class.
	 * @param array                      $installed_plugins The list of the installed and active
	 *                                                      plugins.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Plugin $plugin, array $installed_plugins ) {

		$this->plugin            = $plugin;
		$this->installed_plugins = $installed_plugins;
	}

	/**
	 * Register the integrations instances
	 *
	 * @return $this For concatenation
	 * @since 1.0.0
	 */
	public function register_integrations() {

		$this->installed_plugins_as_assoc_list();

		$available_modules = $this->available_modules();

		// Are there modules installed?
		if ( ! $available_modules ) {
			return $this;
		}

		foreach ( $available_modules as $module ) {
			if ( ! class_exists( self::$modules[ $module ] ) ) {
				continue;
			}

			$this->integrations[] = new self::$modules[ $module ](
				$this->installed_plugins[ $module ]
			);
		}

		return $this;
	}

	/**
	 * Integrate every module
	 *
	 * @return $this For concatenation
	 * @since 1.0.0
	 */
	public function integrate() {

		foreach ( $this->integrations as $integration ) {
			$integration->integrate();
		}

		return $this;
	}

	/**
	 * From index to assoc installed plugins list
	 *
	 * @return void
	 * @since 1.0.0
	 */
	private function installed_plugins_as_assoc_list() {

		$list = [];

		foreach ( $this->installed_plugins as $plugin ) {
			$basename = basename( $plugin, '.php' );

			$list[ $basename ] = $plugin;
		}

		$this->installed_plugins = $list;
	}

	/**
	 * Has Modules
	 *
	 * @return array The existing modules installed or empty array if no modules are available
	 * @since 1.0.0
	 */
	private function available_modules() {

		return array_intersect( array_keys( $this->installed_plugins ), array_keys( self::$modules ) );

		// TODO Add a filter in order to allow third party devs to inject their modules
	}
}
