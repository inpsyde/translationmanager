<?php
/**
 * Plugin Name: translationMANAGER
 * Plugin URI:  https://eurotext.de/en
 * Description: Translate your content from a WordPress Multisite and MultilingualPress.
 * Version:     1.5.0
 * Author:      Inpsyde
 * Author URI:  https://inpsyde.com/
 * Text Domain: translationmanager
 * License:     GPLv2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * Domain Path: /languages
 *
 * @package TranslationManager
 */

// phpcs:disable

use Translationmanager\Plugin;
use Translationmanager\Service\ServiceProviders;

$bootstrap = \Closure::bind( function () {

	/**
	 * Admin Notice
	 *
	 * @param string $message  The message to show in the notice.
	 * @param string $severity The severity of the notice. Can be one of `success`, `warning`, `error`.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function translationmanager_admin_notice( $message, $severity ) {

		printf(
			'<div class="notice notice-%1$s"><p>%2$s</p></div>',
			sanitize_html_class( sanitize_key( $severity ) ),
			wp_kses_post( $message )
		);
	}

	/**
	 * Test Plugin Stuffs
	 *
	 * @return bool True when ok, false otherwise.
	 * @since 1.0.0
	 */
	function translationmanager_plugin_tests_pass() {

		$requirements = new Translationmanager\Requirements();

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

			return false;
		}

		// Show Notice in case Token or URL isn't set.
		if ( ! get_option( \Translationmanager\Setting\PluginSettings::API_KEY ) ) {
			add_action( 'admin_notices', function () use ( $requirements ) {

				translationmanager_admin_notice(
					wp_kses( sprintf( __( // phpcs:ignore
						'TranslationMANAGER seems to be configured incorrectly. Please set a token from %s in order to request translations.',
						'translationmanager'
					),
						'<strong><a href="' . esc_url( menu_page_url( \Translationmanager\Pages\PageOptions::SLUG,
							false ) ) . '">' . esc_html__( 'here', 'translationmanager' ) . '</a></strong>'
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

		return true;
	}

	/**
	 * BootStrap
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function bootstrap() {

		if ( !is_admin() && !is_wp_cli() ) {
			return;
		}

		if ( ! class_exists( Plugin::class ) ) {
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

		if ( ! translationmanager_plugin_tests_pass() ) {
			return;
		}

		$container = new Pimple\Container();

		$container['translationmanager.plugin'] = function () {
			return new Plugin();
		};

		$providers = new ServiceProviders( $container );
		$providers
			->register( new Translationmanager\ProjectItem\ServiceProvider() )
			->register( new Translationmanager\Project\ServiceProvider() )
			->register( new Translationmanager\Pages\ServiceProvider() )
			->register( new Translationmanager\Setting\ServiceProvider() )
			->register( new Translationmanager\MetaBox\ServiceProvider() )
			->register( new Translationmanager\TableList\ServiceProvider() )
			->register( new Translationmanager\Assets\ServiceProvider() )
			->register( new Translationmanager\Request\ServiceProvider() )
			->register( new Translationmanager\SystemStatus\ServiceProvider() )
			->register( new Translationmanager\Activation\ServiceProvider() )
			->register( new Translationmanager\Module\ServiceProvider() )
			->register( new Translationmanager\Xliff\ServiceProvider() );

		$providers
			->bootstrap()
			->integrate();

		unset( $container );
	}

    /**
     * Check if is WP_CLI
     *
     * @return bool
     * @since 1.2.1
     */
    function is_wp_cli() {
        return defined('WP_CLI') && WP_CLI;
    }

	/**
	 * Activate Plugin
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function activate() {

		add_action( 'activated_plugin', function ( $plugin ) {

			if ( plugin_basename( __FILE__ ) === $plugin ) {
				bootstrap();
			}
		}, 0 );
	}

    $rootDir = dirname(__FILE__);

    if (file_exists("$rootDir/vendor/autoload.php")) {
        /* @noinspection PhpIncludeInspection */
        require_once "$rootDir/vendor/autoload.php";
    }

	add_action( 'plugins_loaded', 'bootstrap', - 1 );

	register_activation_hook( __FILE__, 'activate' );
}, null );

$bootstrap();
