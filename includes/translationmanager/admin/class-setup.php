<?php
/**
 * PHP 5.4
 */

namespace Translationmanager\Admin;

class Setup {
	public function plugin_activate() {
		$plugin_data = get_plugin_data( TRANSLATIONMANAGER_FILE );

		if ( ! isset( $plugin_data['Version'] ) || ! $plugin_data['Version'] ) {
			throw new \Exception( 'Bad plugin data.' );
		}

		$version = $plugin_data['Version'];

		$setup_files = glob( TRANSLATIONMANAGER_DIR . '/admin/plugin-activate/*-*.php' );
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