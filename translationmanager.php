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

define( 'TRANSLATIONMANAGER_FILE', __FILE__ );
define( 'TRANSLATIONMANAGER_DIR', dirname( TRANSLATIONMANAGER_FILE ) );
define( 'TRANSLATIONMANAGER_FILENAME', basename( TRANSLATIONMANAGER_DIR ) . '/' . basename( TRANSLATIONMANAGER_FILE ) );
define( 'TRANSLATIONMANAGER_VERSION', '1.0.0' );

add_action( 'plugins_loaded', function () {

	if ( ! is_admin() ) {
		return;
	}

	// Register autoloader.
	require_once __DIR__ . '/includes/translationmanager/class-loader.php';
	spl_autoload_register( array( new \Translationmanager\Loader(), 'load_class' ) );

	// Require composer autoloader if exists.
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	}

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

	// Register Activation.
	register_activation_hook( TRANSLATIONMANAGER_FILENAME, 'translationmanager_activate' );
} );

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
