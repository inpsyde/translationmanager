<?php

namespace Translationmanager\Functions;

use Translationmanager\Plugin;

/**
 * Include Modules
 *
 * @since 1.0.0
 *
 * @return void
 */
function include_modules() {

	$plugins = get_option( 'active_plugins', [] );

	if ( ! $plugins || function_exists( 'wp_get_active_network_plugins' ) ) {
		$plugins = array_merge( $plugins, wp_get_active_network_plugins() );
	}

	// Load module for each active plugin.
	foreach ( $plugins as $plugin ) {
		// Module file is located in "modules/{plugin-slug}.php".
		$module_file = ( new Plugin() )->dir( '/inc/modules/' ) . '/' . basename( dirname( $plugin ) ) . '.php';

		if ( ! file_exists( $module_file ) ) {
			// No module for this plugin so we ignore it.
			continue;
		}

		require_once $module_file;
	}
}
