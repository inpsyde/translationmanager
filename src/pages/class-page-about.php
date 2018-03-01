<?php
/**
 * Page About
 *
 * @package Translationmanager\Pages
 */

namespace Translationmanager\Pages;

use Translationmanager\Plugin;

/**
 * Class Page_About
 *
 * @since   1.0.0
 * @package Translationmanager\Pages
 */
class Page_About {

	/**
	 * Plugin
	 *
	 * @since 1.0.0
	 *
	 * @var \Translationmanager\Plugin
	 */
	private $plugin;

	/**
	 * Page_About constructor
	 *
	 * @since 1.0.0
	 *
	 * @param \Translationmanager\Plugin $plugin The instance of the plugin class.
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

		add_action( 'admin_menu', [ $this, 'add_menu_page' ] );
		add_action( 'admin_head', [ $this, 'enqueue_styles' ] );
	}

	/**
	 * Enqueue Styles
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_styles() {

		wp_register_style(
			'translationmanager-page-about',
			$this->plugin->url( '/resources/css/page-about.css' ),
			[],
			filemtime( $this->plugin->dir( '/resources/css/page-about.css' ) ),
			'screen'
		);
	}

	/**
	 * Add Menu Page
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_menu_page() {

		add_submenu_page(
			'edit.php?post_type=tm_cart',
			esc_html__( 'About', 'translationmanager' ),
			esc_html__( 'About', 'translationmanager' ),
			'manage_options',
			'inpsyde-translationmanager-about',
			array( $this, 'page_callback' )
		);
	}

	/**
	 * Page Callback
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function page_callback() {

		wp_enqueue_style( 'translationmanager-page-about' );

		require_once \Translationmanager\Functions\get_template( '/views/page-about.php' );
	}
}
