<?php

// Register autoloader.
require_once dirname( __FILE__ ) . '/includes/tm4mlp/class-loader.php';
spl_autoload_register( array( new \Tm4mlp\Loader(), 'load_class' ) );

/**
 * Resolve path to template.
 *
 * Makes it possible for themes or other plugins to overwrite a template.
 *
 * @param string $name Required template (relative path from "plugins/pixxio-api/" on).
 *
 * @return string Absolute path to the template.
 */
function tm4mlp_get_template( $name ) {
	$path = TM4MPL_DIR . DIRECTORY_SEPARATOR . $name;

	return apply_filters( 'tm4mlp_get_template', $path, $name );
}