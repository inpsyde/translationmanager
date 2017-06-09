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

define( 'TMWP_FILE', __FILE__ );
define( 'TMWP_DIR', dirname( TMWP_FILE ) );
define( 'TMWP_FILENAME', basename( TMWP_DIR ) . '/' . basename( TMWP_FILE ) );
define( 'TMWP_VERSION', '1.0.0' );

require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'bootstrap.php';

register_activation_hook( TMWP_FILENAME, 'tmwp_activate' );

// Then everything else.
foreach ( glob( TMWP_DIR . '/includes/*.php' ) as $feature ) {
	require_once $feature;
}

if ( ! is_admin() ) {
	return;
}

// In admin context we load some more.
foreach ( glob( TMWP_DIR . '/includes/admin/*.php' ) as $feature ) {
	require_once $feature;
}