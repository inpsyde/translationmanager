<?php

/**
 * gets the current post type in the WordPress Admin
 */
function translationmanager_get_current_post_type() {
	global $post, $typenow, $current_screen;

	if ( $post && $post->post_type ) {
		return $post->post_type;
	}

	if ( $typenow ) {
		return $typenow;
	}

	if ( $current_screen && $current_screen->post_type ) {
		return $current_screen->post_type;
	}

	if ( isset( $_REQUEST['post_type'] ) ) { // Input var okay
		return sanitize_key( $_REQUEST['post_type'] ); // Input var okay
	}

	return null;
}