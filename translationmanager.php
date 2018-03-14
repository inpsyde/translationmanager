<?php
/*
 * Plugin Name: translationMANAGER
 * Plugin URI:  https://eurotext.de/en
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
	if ( is_readable( __DIR__ . '/vendor/autoload.php' ) && ! class_exists( \Translationmanager\Plugin::class ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	}
	if ( ! class_exists( \Translationmanager\Plugin::class ) ) {
		add_action( 'admin_notices', function () {

			translationmanager_admin_notice(
				esc_html__( 'TranslationMANAGER autoloading failed!', 'translationmanager' ),
				'error'
			);
		} );

		return;
	}

	// Require functions and basic files.
	require_once __DIR__ . '/inc/hooks.php';
	foreach ( glob( __DIR__ . '/inc/functions/*.php' ) as $file ) {
		require_once $file;
	}

	$requirements    = new Translationmanager\Requirements();
	$plugin          = new \Translationmanager\Plugin();
	$plugin_settings = new \Translationmanager\Setting\PluginSettings();

	// Check the requirements and in case prevent code execution by returning.
	if ( ! $requirements->is_php_version_ok() ) {
		add_action( 'admin_notices', function () use ( $requirements ) {

			translationmanager_admin_notice( sprintf( esc_html__( // phpcs:ignore
				'TranslationMANAGER requires PHP version %1$s or higher. You are running version %2$s.',
				'translationmanager'
			),
				Translationmanager\Requirements::PHP_MIN_VERSION,
				Translationmanager\Requirements::PHP_CURR_VERSION
			), 'error' );
		} );

		return;
	}

	// Include modules.
	Translationmanager\Functions\include_modules();

	// Register Post Types & Taxonomies.
	( new \Translationmanager\PostType\ProjectItem( $plugin ) )->init();
	( new \Translationmanager\Taxonomy\Project() )->init();

	// Add Pages.
	( new \Translationmanager\Pages\Project() )->init();
	( new \Translationmanager\Pages\PluginMainPage( $plugin ) )->init();
	( new \Translationmanager\Pages\PageOptions( $plugin, $plugin_settings ) )->init();


	// Show Notice in case Token or URL isn't set.
	if ( ! get_option( \Translationmanager\Setting\PluginSettings::API_KEY ) ) {
		add_action( 'admin_notices', function () use ( $requirements ) {

			translationmanager_admin_notice(
				wp_kses( sprintf( __( // phpcs:ignore
					'TranslationMANAGER seems not configured correctly. Please set a token from %s to be able to request translations.',
					'translationmanager'
				),
					'<strong><a href="' . esc_url( menu_page_url( \Translationmanager\Pages\PageOptions::SLUG, false ) ) . '">' . esc_html__( 'here', 'translationmanager' ) . '</a></strong>'
				),
					[
						'a'      => [ 'href' => true ],
						'strong' => [],
					]
				),
				'error'
			);
		} );
	}

	// Meta Boxes.
	( new \Translationmanager\MetaBox\Translation() )->init();

	// Restrict Manage Posts.
	( new \Translationmanager\RestrictManagePosts( $plugin ) )->init();

	// Assets.
	( new \Translationmanager\Assets\Translationmanager( $plugin ) )->init();

	// Actions.
	( new \Translationmanager\Action\Api\AddTranslation(
		new \Translationmanager\Auth\AuthRequestValidator(),
		new \Brain\Nonces\WpNonce( 'add_translation' )
	) )->init();
	( new \Translationmanager\Action\Api\OrderProject(
		new \Translationmanager\Auth\AuthRequestValidator(),
		new \Brain\Nonces\WpNonce( 'order_project' )
	) )->init();
	( new \Translationmanager\Action\Api\UpdateProjectOrderStatus(
		new \Translationmanager\Auth\AuthRequestValidator(),
		new \Brain\Nonces\WpNonce( 'update_project' )
	) )->init();
	( new \Translationmanager\Action\Api\ImportProject(
		new \Translationmanager\Auth\AuthRequestValidator(),
		new \Brain\Nonces\WpNonce( 'import_project' )
	) )->init();

	// System Status.
	( new \Translationmanager\SystemStatus\Controller( $plugin ) )->init();

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
