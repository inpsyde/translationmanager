<?php
/*
Plugin Name: Eurotext API Connector
Plugin URI:  https://eurotext.com
Description: WordPress Plugin to connect to the Eurotext REST API.
Version:     1.0.0
Author:      Inpsyde
Author URI:  https://inpsyde.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: tm4mpl
Domain Path: /languages
*/

define( 'TM4MLP_FILE', __FILE__ );
define( 'TM4MLP_VERSION', '1.0.0' );

require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'bootstrap.php';

register_activation_hook( TM4MLP_FILENAME, 'tm4mlp_activate' );

// Then everything else.
foreach ( glob( TM4MLP_DIR . '/includes/*.php' ) as $feature ) {
	require_once $feature;
}

if ( ! is_admin() ) {
	return;
}

// In admin context we load some more.
foreach ( glob( TM4MLP_DIR . '/includes/admin/*.php' ) as $feature ) {
	require_once $feature;
}