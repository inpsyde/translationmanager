<?php
// If the file was already loaded (e.g. via Composer) the constant is defined, and we bail to avoid fatals.
if ( defined( 'TRANSLATIONMANAGER_ACTION_PROJECT_ADD_TRANSLATION' ) ) {
	return;
}

// Register autoloader.
require_once dirname( __FILE__ ) . '/includes/translationmanager/class-loader.php';
spl_autoload_register( array( new \Translationmanager\Loader(), 'load_class' ) );

/**
 * Resolve path to template.
 *
 * Makes it possible for themes or other plugins to overwrite a template.
 *
 * @param string $name Required template (relative path from "plugins/translationmanager/" on).
 *
 * @return string Absolute path to the template.
 */
function translationmanager_get_template( $name ) {
	$path = TRANSLATIONMANAGER_DIR . DIRECTORY_SEPARATOR . $name;

	return apply_filters( 'translationmanager_get_template', $path, $name );
}

/**
 * Activation function.
 *
 * Proxy to the plugin activation.
 * This is a function so that it can be unregistered by other plugins
 * as objects can not be unregistered
 * and static methods are considered as bad coding style / hard to test.
 */
function translationmanager_activate() {
	$setup = new \Translationmanager\Admin\Setup();
	$setup->plugin_activate();
}

function translationmanager_die( $message = '', $title = '', $args = array() ) {
	if ( ! $title ) {
		$title = __( 'We are sorry!', 'translationmanager' );
	}

	if ( ! $message ) {
		$message = __( 'Something went wrong. Please contact us.', 'translationmanager' );
	}

	wp_die( $message, $title, $args );
}

// Set constants during compile time.
const TRANSLATIONMANAGER_ACTION_PROJECT_ADD_TRANSLATION = 'translationmanager_action_project_add_translation';
const TRANSLATIONMANAGER_ACTION_PROJECT_ORDER           = 'translationmanager_action_project_order';
const TRANSLATIONMANAGER_ACTION_PROJECT_UPDATE          = 'translationmanager_action_project_update';
const TRANSLATIONMANAGER_API_PROCESS_ORDER              = 'translationmanager_api_process_order';
const TRANSLATIONMANAGER_CAP_TRANSLATION_REQUEST        = 'edit_others_pages';
const TMANAGER_CART                                     = 'tmanager_cart';
const TRANSLATIONMANAGER_FILTER_BEFORE_ADD_TO_PROJECT   = 'translationmanager_filter_before_add_to_project';
const TRANSLATIONMANAGER_FILTER_PROJECT_ADD_TRANSLATION = 'translationmanager_action_project_add_translation';
const TRANSLATIONMANAGER_INCOMING_DATA                  = 'translationmanager_incoming_data';
const TRANSLATIONMANAGER_ORDER                          = 'tmanager_order';
const TRANSLATIONMANAGER_OUTGOING_DATA                  = 'translationmanager_outgoing_data';
const TRANSLATIONMANAGER_POST_UPDATER                   = 'translationmanager_post_updater';
const TRANSLATIONMANAGER_TAX_PROJECT                    = 'translationmanager_project';
const TRANSLATIONMANAGER_TRANS_STATUS                   = 'translationmanager_trans_status';
const TRANSLATIONMANAGER_TRANS_STATUS_PENDING           = 'translationmanager_pending';
const TRANSLATIONMANAGER_UPDATED_POST                   = 'translationmanager_updated_post';
