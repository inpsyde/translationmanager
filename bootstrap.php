<?php
// If the file was already loaded (e.g. via Composer) the constant is defined, and we bail to avoid fatals.
if ( defined( 'TRANSLATIONMANAGER_ACTION_PROJECT_ADD_TRANSLATION' ) ) {
	return;
}

// Register autoloader.
require_once __DIR__ . '/includes/translationmanager/class-loader.php';
spl_autoload_register( array( new \Translationmanager\Loader(), 'load_class' ) );

// Require composer autoloader if exists.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

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
