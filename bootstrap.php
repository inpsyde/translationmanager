<?php

// Register autoloader.
require_once dirname( __FILE__ ) . '/includes/tm4mlp/class-loader.php';
spl_autoload_register( array( new \Tm4mlp\Loader(), 'load_class' ) );

/**
 * Resolve path to template.
 *
 * Makes it possible for themes or other plugins to overwrite a template.
 *
 * @param string $name Required template (relative path from "plugins/tm4mlp/" on).
 *
 * @return string Absolute path to the template.
 */
function tm4mlp_get_template( $name ) {
	$path = TM4MLP_DIR . DIRECTORY_SEPARATOR . $name;

	return apply_filters( 'tm4mlp_get_template', $path, $name );
}

/**
 * Activation function.
 *
 * Proxy to the plugin activation.
 * This is a function so that it can be unregistered by other plugins
 * as objects can not be unregistered
 * and static methods are considered as bad coding style / hard to test.
 */
function tm4mlp_activate() {
	$setup = new \Tm4mlp\Admin\Setup();
	$setup->plugin_activate();
}

function tm4mlp_die( $message = '', $title = '', $args = array() ) {
	if ( ! $title ) {
		$title = __( 'We are sorry!', 'tm4mlp' );
	}

	if ( ! $message ) {
		$message = __( 'Something went wrong. Please contact us.', 'tm4mlp' );
	}

	wp_die( $message, $title, $args );
}

// Set constants during compile time.
const TM4MLP_ACTION_PROJECT_ADD_TRANSLATION = 'tm4mlp_action_project_add_translation';
const TM4MLP_ACTION_PROJECT_ORDER           = 'tm4mlp_action_project_order';
const TM4MLP_API_PROCESS_ORDER              = 'tm4mlp_api_process_order';
const TM4MLP_CAP_TRANSLATION_REQUEST        = 'edit_others_pages';
const TM4MLP_CART                           = 'tm4mlp_cart';
const TM4MLP_ORDER                          = 'tm4mlp_order';
const TM4MLP_SANITIZE_POST                  = 'tm4mlp_sanitize_post';
const TM4MLP_TAX_PROJECT                    = 'tm4mlp_project';
const TM4MLP_TRANS_STATUS                   = 'tm4mlp_trans_status';
const TM4MLP_TRANS_STATUS_PENDING           = 'tm4mlp_pending';
