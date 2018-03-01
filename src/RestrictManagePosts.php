<?php
/**
 * Restrict Manage Posts
 *
 * @since     1.0.0
 * @package   Translationmanager
 */

namespace Translationmanager;

/**
 * Class RestrictManagePosts
 *
 * @since   1.0.0
 * @package Translationmanager
 */
class RestrictManagePosts {

	/**
	 * Plugin
	 *
	 * @since 1.0.0
	 *
	 * @var \Translationmanager\Plugin
	 */
	private $plugin;

	/**
	 * RestrictManagePosts constructor
	 *
	 * @since 1.0.0
	 *
	 * @param \Translationmanager\Plugin $plugin The plugin instance.
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

		add_action( 'manage_posts_extra_tablenav', [ $this, 'restrict_manage_posts' ], 10 );
		add_action( 'admin_head', [ $this, 'enqueue_styles' ], 10 );
		add_action( 'admin_head', [ $this, 'enqueue_scripts' ], 10 );
		add_action( 'manage_posts_extra_tablenav', [ $this, 'restrict_manage_posts' ], 10 );
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
			'translationmanager-restrict-manage-posts',
			$this->plugin->url( '/resources/css/restrict-manage-posts.css' ),
			[],
			filemtime( $this->plugin->dir( '/resources/css/restrict-manage-posts.css' ) ),
			'screen'
		);
	}

	/**
	 * Enqueue Scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		wp_register_script(
			'translationmanager-restrict-manage-posts',
			$this->plugin->url( '/resources/js/restrict-manage-posts.js' ),
			[],
			filemtime( $this->plugin->dir( '/resources/js/restrict-manage-posts.js' ) ),
			true
		);
	}

	/**
	 * Restrict Manage Posts
	 *
	 * @since 1.0.0
	 *
	 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
	 *
	 * @return void
	 */
	public function restrict_manage_posts( $which ) {

		if ( 'top' !== $which ) {
			return;
		}

		wp_enqueue_style( 'translationmanager-restrict-manage-posts' );
		wp_enqueue_script( 'translationmanager-restrict-manage-posts' );

		require_once \Translationmanager\Functions\get_template( '/views/restrict-manage-posts.php' );
	}
}
