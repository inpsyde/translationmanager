<?php

/**
 * Plugin Main Page
 *
 * @since   1.0.0
 * @package Translationmanager\Pages
 */

namespace Translationmanager\Pages;

use Translationmanager\Plugin;

/**
 * Class PluginMainPage
 *
 * @since   1.0.0
 * @package Translationmanager\Pages
 */
class PluginMainPage implements Page {

	/**
	 * Page Slug
	 *
	 * @since 1.0.0
	 *
	 * @var string The page slug
	 */
	const SLUG = 'translationmanager';

	/**
	 * Plugin
	 *
	 * @since 1.0.0
	 *
	 * @var \Translationmanager\Plugin The instance of the plugin
	 */
	private $plugin;

	/**
	 * PluginMainPage constructor
	 *
	 * @since 1.0.0
	 *
	 * @param \Translationmanager\Plugin $plugin The instance of the plugin.
	 */
	public function __construct( Plugin $plugin ) {

		$this->plugin = $plugin;
	}

	/**
	 * Set hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {

		add_action( 'admin_menu', [ $this, 'add_page' ] );
		add_action( 'network_admin_menu', [ $this, 'add_page' ] );
	}

	/**
	 * @inheritdoc
	 */
	public function add_page() {

		add_menu_page(
			esc_html__( 'Translations', 'translationmanager' ),
			esc_html__( 'Translations', 'translationmanager' ),
			$this->capability(),
			self::SLUG,
			'__return_false',
			$this->plugin->url( '/resources/img/tm-icon-bw.png' ),
			100
		);
	}

	/**
	 * @inheritdoc
	 */
	public static function url() {

		return is_multisite() && current_user_can( 'manage_network_options' ) ?
			network_admin_url( 'admin.php?page=' . self::SLUG ) :
			menu_page_url( self::SLUG, false );
	}

	/**
	 * @inheritdoc
	 */
	public function render_template() {
		// Nothing here for now.
	}

	/**
	 * Capability
	 *
	 * Retrieve the capability based on context. Network or not.
	 *
	 * @return string The capability to check against
	 */
	private function capability() {

		return ( is_network_admin() ? 'manage_network_options' : 'manage_options' );
	}
}
