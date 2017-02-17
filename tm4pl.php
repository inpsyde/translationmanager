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

define( 'EUROTEXT_API_TEXTDOMAIN', 'tm4mpl' );
define( 'EUROTEXT_API_FILE', __FILE__ );
define( 'EUROTEXT_API_DIR', dirname( __FILE__ ) );

// Register autoloader.
// require_once __DIR__ . '/includes/eurotext/class-loader.php';
// spl_autoload_register( array( new \Eurotext\Loader(), 'load_class' ) );

// register_activation_hook( EUROTEXT_API_FILE, array( '\\Eurotext\\Admin\\Setup', 'plugin_activate' ) );

// Then everything else.
foreach ( glob( EUROTEXT_API_DIR . '/includes/*.php' ) as $feature ) {
	require_once $feature;
}
