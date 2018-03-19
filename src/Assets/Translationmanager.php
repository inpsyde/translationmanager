<?php
/**
 * Asset Translationmanager
 *
 * @since   1.0.0
 * @package Translationmanager\Assets
 */

namespace Translationmanager\Assets;

use Translationmanager\Plugin;

/**
 * Class Translationmanager
 *
 * @since   1.0.0
 * @package Translationmanager\Assets
 */
class Translationmanager {

	/**
	 * Plugin
	 *
	 * @since 1.0.0
	 *
	 * @var \Translationmanager\Plugin Instance of the class
	 */
	private $plugin;

	/**
	 * Translationmanager constructor
	 *
	 * @since 1.0.0
	 *
	 * @param \Translationmanager\Plugin $plugin Instance of the class.
	 */
	public function __construct( Plugin $plugin ) {

		$this->plugin = $plugin;
	}

	/**
	 * Register Style
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_style() {

		wp_enqueue_style(
			'translationmanager',
			$this->plugin->url( '/assets/css/translationmanager.css' ),
			[],
			filemtime( $this->plugin->dir( '/assets/css/translationmanager.css' ) ),
			'screen'
		);
	}
}
