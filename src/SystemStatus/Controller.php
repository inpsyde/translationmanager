<?php
/**
 * System Status
 *
 * @since   1.0.0
 * @package Translationmanager
 */

namespace Translationmanager\SystemStatus;

/**
 * Class Controller
 *
 * @since   1.0.0
 * @package Translationmanager
 */
class Controller {

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
