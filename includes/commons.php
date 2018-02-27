<?php
/**
 * Common Functions
 */

/**
 * Resolve path to template.
 *
 * Makes it possible for themes or other plugins to overwrite a template.
 *
 * @since 1.0.0
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
 *
 * @since 1.0.0
 */
function translationmanager_activate() {

	$setup = new \Translationmanager\Admin\Setup();
	$setup->plugin_activate();
}

/**
 * Translation Manager Die
 *
 * It's a wrapper for `wp_die`.
 *
 * @since 1.0.0
 *
 * @param string $message The message.
 * @param string $title   The title.
 * @param array  $args    Additiona arguments.
 */
function translationmanager_die( $message = '', $title = '', $args = array() ) {

	if ( ! $title ) {
		$title = __( 'We are sorry!', 'translationmanager' );
	}

	if ( ! $message ) {
		$message = __( 'Something went wrong. Please contact us.', 'translationmanager' );
	}

	wp_die( wp_kses_post( $message ), $title, $args );
}
