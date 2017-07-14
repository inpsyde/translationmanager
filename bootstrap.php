<?php

// Register autoloader.
require_once dirname( __FILE__ ) . '/includes/tmwp/class-loader.php';
spl_autoload_register( array( new \Tmwp\Loader(), 'load_class' ) );

/**
 * Resolve path to template.
 *
 * Makes it possible for themes or other plugins to overwrite a template.
 *
 * @param string $name Required template (relative path from "plugins/tmwp/" on).
 *
 * @return string Absolute path to the template.
 */
function tmwp_get_template( $name ) {
	$path = TMWP_DIR . DIRECTORY_SEPARATOR . $name;

	return apply_filters( 'tmwp_get_template', $path, $name );
}

/**
 * Activation function.
 *
 * Proxy to the plugin activation.
 * This is a function so that it can be unregistered by other plugins
 * as objects can not be unregistered
 * and static methods are considered as bad coding style / hard to test.
 */
function tmwp_activate() {
	$setup = new \Tmwp\Admin\Setup();
	$setup->plugin_activate();
}

function tmwp_die( $message = '', $title = '', $args = array() ) {
	if ( ! $title ) {
		$title = __( 'We are sorry!', 'tmwp' );
	}

	if ( ! $message ) {
		$message = __( 'Something went wrong. Please contact us.', 'tmwp' );
	}

	wp_die( $message, $title, $args );
}

// Set constants during compile time.
const TMWP_ACTION_PROJECT_ADD_TRANSLATION = 'tmwp_action_project_add_translation';
const TMWP_ACTION_PROJECT_ORDER           = 'tmwp_action_project_order';
const TMWP_ACTION_PROJECT_UPDATE          = 'tmwp_action_project_update';
const TMWP_API_PROCESS_ORDER              = 'tmwp_api_process_order';
const TMWP_CAP_TRANSLATION_REQUEST        = 'edit_others_pages';
const TMWP_CART                           = 'tmwp_cart';
const TMWP_FILTER_BEFORE_ADD_TO_PROJECT   = 'tmwp_filter_before_add_to_project';
const TMWP_FILTER_PROJECT_ADD_TRANSLATION = 'tmwp_action_project_add_translation';
const TMWP_INCOMING_DATA                  = 'tmwp_incoming_data';
const TMWP_ORDER                          = 'tmwp_order';
const TMWP_OUTGOING_DATA                  = 'tmwp_outgoing_data';
const TMWP_POST_UPDATER                   = 'tmwp_post_updater';
const TMWP_TAX_PROJECT                    = 'tmwp_project';
const TMWP_TRANS_STATUS                   = 'tmwp_trans_status';
const TMWP_TRANS_STATUS_PENDING           = 'tmwp_pending';
const TMWP_UPDATED_POST                   = 'tmwp_updated_post';
