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
	 * Set Hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {

		add_action( 'admin_head', [ $this, 'register_style' ] );
	}

	/**
	 * Register Style
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_style() {

		wp_register_style(
			'translationmanager',
			$this->plugin->url( '/resources/css/translationmanager.css' ),
			[],
			filemtime( $this->plugin->dir( '/resources/css/translationmanager.css' ) ),
			'screen'
		);

		wp_enqueue_style( 'translationmanager' );
	}
}
