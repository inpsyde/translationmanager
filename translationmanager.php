<?php
/*
Plugin Name: translationMANAGER for WordPress
Plugin URI:  https://eurotext.com
Description: Translate your contents in a WordPress Multisite and MultilingualPress.
Version:     1.0.0
Author:      Inpsyde
Author URI:  https://inpsyde.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: tm4mpl
Domain Path: /languages
*/

define( 'TRANSLATIONMANAGER_FILE', __FILE__ );
define( 'TRANSLATIONMANAGER_DIR', dirname( TRANSLATIONMANAGER_FILE ) );
define( 'TRANSLATIONMANAGER_FILENAME', basename( TRANSLATIONMANAGER_DIR ) . '/' . basename( TRANSLATIONMANAGER_FILE ) );
define( 'TRANSLATIONMANAGER_VERSION', '1.0.0' );

/**
 * Checking if vendor/autoload.php exists or not.
 */
if ( file_exists( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'vendor/autoload.php' ) ) {
	// Bootstrap (also loads "bootstrap.php").
	require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';
}

register_activation_hook( TRANSLATIONMANAGER_FILENAME, 'translationmanager_activate' );

// Then everything else.
foreach ( glob( TRANSLATIONMANAGER_DIR . '/includes/*.php' ) as $feature ) {
	require_once $feature;
}

if ( ! is_admin() ) {
	return;
}

// In admin context we load some more.
foreach ( glob( TRANSLATIONMANAGER_DIR . '/includes/admin/*.php' ) as $feature ) {
	require_once $feature;
}