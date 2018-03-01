<?php
/**
 * Setup
 *
 * @since   1.0.0
 * @package Translationmanager\Admin
 */

namespace Translationmanager\Admin;

use Translationmanager\Plugin;

/**
 * Class Setup
 *
 * @since   1.0.0
 * @package Translationmanager\Admin
 */
class Setup {

	/**
	 * Activate plugin
	 *
	 * @since 1.0.0
	 *
	 * @throws \Exception In case the plugin version isn't set.
	 */
	public function plugin_activate() {

		$plugin      = new Plugin();
		$plugin_data = get_plugin_data( $plugin->file_path() );

		if ( ! isset( $plugin_data['Version'] ) || ! $plugin_data['Version'] ) {
			throw new \Exception( 'Bad plugin data.' );
		}

		$version = $plugin_data['Version'];

		$setup_files = glob( $plugin->dir( '/inc/plugin-activate/*.php' ) );
		natsort( $setup_files );

		$current_version = get_option( 'translationmanager_version', '0.0.0' );

		foreach ( $setup_files as $setup_script ) {
			$file_version = strtok( basename( $setup_script ), '-' );

			if ( version_compare( $current_version, $file_version ) >= 0 ) {
				// Current version is bigger than or equal to file version so we skip it.
				continue;
			}

			if ( version_compare( $file_version, $version ) > 0 ) {
				// File version is bigger than plugin version so we skip future scripts.
				continue;
			}

			require_once $setup_script;

			update_option( 'translationmanager_version', $file_version );
		}
	}
}