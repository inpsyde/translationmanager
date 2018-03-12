<?php
/**
 * System Status
 *
 * @since   1.0.0
 * @package Translationmanager
 */

namespace Translationmanager\SystemStatus;

use Translationmanager\Plugin;

/**
 * Class Controller
 *
 * @since   1.0.0
 * @package Translationmanager
 */
class Controller {

	/**
	 * Plugin
	 *
	 * @since 1.0.0
	 *
	 * @var \Translationmanager\Plugin The plugin instance
	 */
	private $plugin;

	/**
	 * Information Class Names
	 *
	 * @since 1.0.0
	 *
	 * @var array The list of the information we want to show to the user.
	 */
	private static $informations = [
		'\\Inpsyde\\SystemStatus\\Data\\Php',
		'\\Translationmanager\\SystemStatus\\Translationmanager',
		'\\Inpsyde\\SystemStatus\\Data\\Wordpress',
		'\\Inpsyde\\SystemStatus\\Data\\Database',
		'\\Inpsyde\\SystemStatus\\Data\\Plugins',
	];

	/**
	 * Controller constructor
	 *
	 * @since 1.0.0
	 *
	 * @param \Translationmanager\Plugin $plugin The plugin instance.
	 */
	public function __construct( Plugin $plugin ) {

		$this->plugin = $plugin;
	}

	/**
	 * Create the System Status instance with information
	 *
	 * @since 1.0.0
	 *
	 * @return \Inpsyde\SystemStatus\Builder
	 */
	public function system_status() {

		return new \Inpsyde\SystemStatus\Builder( self::$informations, 'table' );
	}

	/**
	 * Set Hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {

		( new \Inpsyde\SystemStatus\Page\SystemStatus(
			esc_html__( 'Status', 'translationmanager' ),
			'translationmanager',
			'translationmanager',
			[ $this, 'render' ]
		) )->init();

		( new \Inpsyde\SystemStatus\Assets\Styles( $this->plugin->url( '/vendor/inpsyde/' ), '' ) )->init();
	}

	/**
	 * Render System Status
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render() {

		$this->system_status()
		     ->build()
		     ->render();
	}
}
