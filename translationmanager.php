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
// Load bootstrap
require_once __DIR__ . '/bootstrap.php';

register_activation_hook( TRANSLATIONMANAGER_FILENAME, 'tmwp_activate' );

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