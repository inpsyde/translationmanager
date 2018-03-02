<?php
/*
 * Plugin Name: translationMANAGER
 * Plugin URI:  https://eurotext.com
 * Description: Translate your contents in a WordPress Multisite and MultilingualPress.
 * Version:     1.0.0
 * Author:      Inpsyde
 * Author URI:  https://inpsyde.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: translationmanager
 * Requires PHP: 5.6
 * Domain Path: /languages
 */

add_action( 'plugins_loaded', function () {

	if ( ! is_admin() ) {
		return;
	}

	// Require composer autoloader if exists.
	if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		return;
	}
	require_once __DIR__ . '/vendor/autoload.php';

	$requirements = new Translationmanager\Requirements();
	// Check the requirements and in case prevent code execution by returning.
	if ( ! $requirements->is_php_version_ok() ) {
		add_action( 'admin_notices', function () use ( $requirements ) {

			translationmanager_admin_notice( sprintf( esc_html__( // phpcs:ignore
				'Inpsyde Google Tag Manager requires PHP version %1$s or higher. You are running version %2$s.',
				'translationmanager'
			),
				Translationmanager\Requirements::PHP_MIN_VERSION,
				Translationmanager\Requirements::PHP_CURR_VERSION
			), 'error' );
		} );

		return;
	}

	$plugin = new \Translationmanager\Plugin();

	// Include modules.
	Translationmanager\Functions\include_modules();

	// Initialize Options Page.
	( new \Translationmanager\Pages\PageOptions() )->init();

	// Add Pages.
	( new \Translationmanager\Pages\PageAbout( $plugin ) )->init();

	// Restrict Manage Posts.
	( new \Translationmanager\RestrictManagePosts( $plugin ) )->init();

	// Assets.
	( new \Translationmanager\Assets\Translationmanager( $plugin ) )->init();

	// Actions.
	( new \Translationmanager\Action\AddTranslationActionHandler(
		new \Translationmanager\Auth\AuthRequestValidator(),
		new \Brain\Nonces\WpNonce( 'add_translation' )
	) )->init();
	( new \Translationmanager\Action\OrderProjectActionHandler(
		new \Translationmanager\Auth\AuthRequestValidator(),
		new \Brain\Nonces\WpNonce( 'order_translation' )
	) )->init();

	// Register Activation.
	register_activation_hook( $plugin->file_path(), 'translationmanager_activate' );
}, - 1 );

/**
 * Admin Notice
 *
 * @since 1.0.0
 *
 * @param string $message  The message to show in the notice.
 * @param string $severity The severity of the notice. Can be one of `success`, `warning`, `error`.
 *
 * @return void
 */
function translationmanager_admin_notice( $message, $severity ) {

	printf(
		'<div class="notice notice-%1$s"><p>%2$s</p></div>',
		sanitize_html_class( sanitize_key( $severity ) ),
		wp_kses_post( $message )
	);
}

/**
 * Activation function.
 *
 * Proxy to the plugin activation.
 * This is a function so that it can be unregistered by other plugins
 * as objects can not be unregistered
 * and static methods are considered as bad coding style / hard to test.
 *
 * @since 1.0.0
 *
 * @throws \Exception In case of plugin contain invalid data.
 *
 * @return void
 */
function translationmanager_activate() {

	( new \Translationmanager\PluginActivate() )->store_version();
}
