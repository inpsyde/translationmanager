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
	$path = TM4MLP_DIR . DIRECTORY_SEPARATOR . $name;

	return apply_filters( 'tm4mlp_get_template', $path, $name );
}

const TM4MLP_CAP_TRANSLATION_REQUEST = 'edit_others_pages';
const TM4MLP_TRANS_STATUS            = 'tm4mlp_trans_status';
const TM4MLP_TRANS_STATUS_PENDING    = 'tm4mlp_pending';