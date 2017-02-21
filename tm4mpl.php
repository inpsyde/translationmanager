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
define( 'TM4MLP_DIR', dirname( TM4MLP_FILE ) );
define( 'TM4MLP_FILENAME', basename( TM4MLP_DIR ) . '/' . basename( __FILE__ ) );

require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'bootstrap.php';

// register_activation_hook( TM4MPL_API_FILE, array( '\\Eurotext\\Admin\\Setup', 'plugin_activate' ) );

// Then everything else.
foreach ( glob( TM4MLP_DIR . '/includes/*.php' ) as $feature ) {
	require_once $feature;
}
