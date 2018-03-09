<?php
/**
 * Containing class that handles pixxio options page.
 *
 * @package Translationmanager
 */

namespace Translationmanager\Pages;

use Brain\Nonces\WpNonce;
use Translationmanager\Action\SiteOptionsHandler;
use Translationmanager\Auth\AuthRequestValidator;
use Translationmanager\Functions;
use Translationmanager\Plugin;
use Translationmanager\Setting;

/**
 * Controller / Model for translationmanager options page.
 *
 * @package Translationmanager\Admin
 */
class PageOptions implements Page {

	const USERNAME = 'translationmanager_api_username';
	const PASSWORD = 'translationmanager_api_password';

	const TRANSIENT_CATEGORIES = 'translationmanager_categories';
	const SELECTED_CATEGORIES  = 'translationmanager_sync_categories';
	const SLUG                 = 'translationmanager_settings';

	/**
	 * Allowed actions
	 *
	 * If this is a field in the post data,
	 * then the equally names function will be called with the post data.
	 * Usually those is a list of buttons that can be pressed on the options page.
	 *
	 * @var string[]
	 */
	private $actions = [
		'fetch_files',
		'save',
		'save_categories',
		'update_categories',
	];

	private $plugin;

	/**
	 * Plugin Settings
	 *
	 * @since 1.0.0
	 *
	 * @var Setting\PluginSettings The instance of the class
	 */
	private $settings;

	/**
	 * PageOptions constructor
	 *
	 * @since 1.0.0
	 *
	 * @param Plugin                 $plugin   The plugin instance.
	 * @param Setting\PluginSettings $settings The plugin settings.
	 */
	public function __construct( Plugin $plugin, Setting\PluginSettings $settings ) {

		$this->plugin   = $plugin;
		$this->settings = $settings;
	}

	/**
	 * Set Hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {

		add_action( 'admin_menu', [ $this, 'add_page' ] );
		add_action( 'network_admin_menu', [ $this, 'add_page' ] );
		add_action( 'admin_head', [ $this, 'enqueue_style' ] );
		add_action( 'admin_head', [ $this, 'enqueue_script' ] );
		add_action( 'translations_page_translationmanager_settings', [
			new SiteOptionsHandler( new AuthRequestValidator(), new WpNonce( 'translationmanager_siteoptions' ) ),
			'handle',
		] );
		add_action( 'translations_page_translationmanager_settings', [ $this->settings, 'register_setting' ] );

		add_filter( 'option_page_capability_' . Setting\PluginSettings::OPTION_GROUP, [
			$this,
			'filter_capabilities',
		] );
	}

	/**
	 * @inheritdoc
	 */
	public function add_page() {

		add_submenu_page(
			'translationmanager',
			esc_html__( 'Settings', 'translationmanager' ),
			esc_html__( 'Settings', 'translationmanager' ),
			$this->capability(),
			self::SLUG,
			[ $this, 'render_template' ]
		);
	}

	/**
	 * @inheritdoc
	 */
	public static function url() {

		return is_multisite() && current_user_can( 'manage_network_options' ) && Functions\is_plugin_active_for_network()
			? network_admin_url( 'admin.php?page=' . self::SLUG )
			: menu_page_url( self::SLUG, false );
	}

	/**
	 * @inheritdoc
	 */
	public function render_template() {

		if ( ! current_user_can( $this->capability() ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'translatemanager' ) );
		}

		wp_enqueue_style( 'translationmanager-options-page' );

		// Render the template.
		require_once Functions\get_template( '/views/options-page/layout.php' );
	}

	/**
	 * Fetch token.
	 *
	 * Only / Best current point to hook in settings update process it the option page cap filter.
	 *
	 * @param string[] $capabilities A list of capabilities.
	 *
	 * @return \string[]
	 */
	public function filter_capabilities( $capabilities ) {

		if ( ! check_admin_referer( Setting\PluginSettings::OPTION_GROUP . '-options' ) ) {
			// Seems like some other page so we won't do stuff.
			return $capabilities;
		}

		$login_data = $_REQUEST; // input var okay

		// Buttons the user can press.
		$chosen_action = array_intersect( array_keys( $login_data ), $this->actions );

		if ( $chosen_action ) {
			$chosen_action = current( $chosen_action ) . '_action';
			$this->$chosen_action( $login_data );
		}

		return $capabilities;
	}

	/**
	 * Enqueue Style
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_style() {

		wp_register_style(
			'translationmanager-options-page',
			( new Plugin() )->url( '/assets/css/settings.css' ),
			[],
			filemtime( ( new Plugin() )->dir( '/assets/css/settings.css' ) ),
			'screen'
		);
	}

	/**
	 * Enqueue Script
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_script() {

		wp_enqueue_script(
			'translationmanager-options-page',
			( new Plugin() )->url( '/resources/js/options-page.js' ),
			[ 'jquery', 'jquery-ui-tabs' ],
			filemtime( ( new Plugin() )->dir( '/resources/js/options-page.js' ) ),
			true
		);
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

	/**
	 * Nonce
	 *
	 * @since 1.0.0
	 *
	 * @return \Brain\Nonces\WpNonce The nonce instance
	 */
	private function nonce() {

		return new \Brain\Nonces\WpNonce( 'translationmanager_siteoptions' );
	}

	/**
	 * Form Action
	 *
	 * Retrieve the form action based on context. Network or not.
	 *
	 * @since 1.0.0
	 *
	 * @return string The url for the from action
	 */
	private function action() {

		if ( is_network_admin() ) {
			return Functions\current_url();
		}

		return admin_url( 'options.php' );
	}
}
