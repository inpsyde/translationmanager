<?php
/**
 * Loading modules for each activated plugin.
 */


$plugins = get_option( 'active_plugins' );
if ( ! $plugins && function_exists( 'wp_get_active_network_plugins' ) ) {
	$plugins = wp_get_active_network_plugins();
}

// Load module for each active plugin.
foreach ( $plugins as $plugin ) {
	// Module file is located in "modules/{plugin-slug}.php".
	$module_file = dirname( __FILE__ )
	               . DIRECTORY_SEPARATOR . 'modules'
	               . DIRECTORY_SEPARATOR . dirname( $plugin ) . '.php';

	if ( ! file_exists( $module_file ) ) {
		// No module for this plugin so we ignore it.
		continue;
	}

	require_once $module_file;
}