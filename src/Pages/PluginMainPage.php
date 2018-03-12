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
	}

	/**
	 * @inheritdoc
	 */
	public function add_page() {

		global $submenu;

		add_menu_page(
			esc_html__( 'Translations', 'translationmanager' ),
			esc_html__( 'Translations', 'translationmanager' ),
			'manage_options',
			self::SLUG,
			'__return_false',
			$this->plugin->url( '/resources/img/tm-icon-bw.png' ),
			100
		);

		add_submenu_page(
			'translationmanager',
			esc_html__( 'Projects', 'translationmanager' ),
			esc_html__( 'Projects', 'translationmanager' ),
			'manage_options',
			'translationmanager-project',
			'__return_false'
		);

		// User may not allowed, so the index may not exists.
		if ( isset( $submenu['translationmanager'] ) ) {
			$submenu['translationmanager'][0][2] = admin_url( // phpcs:ignore
				'edit-tags.php?taxonomy=translationmanager_project&post_type=project_item'
			);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function render_template() {
		// Nothing here for now.
	}
}
