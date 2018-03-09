<?php
/**
 * Common Functions
 */

namespace Translationmanager\Functions;

use Translationmanager\Plugin;

/**
 * Resolve path to template.
 *
 * @todo  Implements a function that can require the template instead of returing only the path. Alternatively rename
 *        it to `get_template_path`.
 *
 * Makes it possible for themes or other plugins to overwrite a template.
 *
 * @since 1.0.0
 *
 * @param string $name Required template (relative path from "plugins/translationmanager/" on).
 *
 * @return string Absolute path to the template.
 */
function get_template( $name ) {

	/**
	 * Get Template
	 *
	 * @since 1.0.0
	 *
	 * @param string $path The path of the template.
	 * @param string $name The file name of the template.
	 */
	return apply_filters( 'translationmanager_get_template', ( new Plugin() )->dir( $name ), $name );
}

/**
 * Admin Page Redirect
 *
 * @since 1.0.0
 *
 * @param string $page    The page where redirect.
 * @param array  $args    The query arguments to append to the url.
 * @param int    $blog_id The ID of the blog. Optional.
 * @param int    $status  The status to set for the request.
 */
function redirect_admin_page_network( $page, $args, $blog_id = null, $status = 302 ) {

	wp_safe_redirect( get_admin_url( $blog_id, add_query_arg( $args, $page ) ), $status );

	die;
}

/**
 * Sanitize content for allowed HTML tags for post content.
 *
 * @todo  Remove if the issue will be fixed. See below.
 *
 * @see   https://core.trac.wordpress.org/ticket/37085
 *
 * @since 1.0.0
 *
 * @param string $data        Post content to filter.
 * @param array  $extra_attrs Extra tags allowed.
 *
 * @return string Filtered post content with allowed HTML tags and attributes.
 */
function kses_post( $data, array $extra_attrs = [] ) {

	global $allowedposttags;

	$tags_input_included = array_merge( $allowedposttags, [
		'input'    => [
			'accept'       => true,
			'autocomplete' => true,
			'autofocus'    => true,
			'checked'      => true,
			'class'        => true,
			'disabled'     => true,
			'id'           => true,
			'height'       => true,
			'min'          => true,
			'max'          => true,
			'minlenght'    => true,
			'maxlength'    => true,
			'name'         => true,
			'pattern'      => true,
			'placeholder'  => true,
			'readony'      => true,
			'required'     => true,
			'size'         => true,
			'src'          => true,
			'step'         => true,
			'type'         => true,
			'value'        => true,
			'width'        => true,
		],
		'select'   => [
			'autofocus' => true,
			'class'     => true,
			'id'        => true,
			'disabled'  => true,
			'form'      => true,
			'multiple'  => true,
			'name'      => true,
			'required'  => true,
			'size'      => true,
		],
		'option'   => [
			'disabled' => true,
			'label'    => true,
			'selected' => true,
			'value'    => true,
		],
		'optgroup' => [
			'disabled' => true,
			'label'    => true,
		],
		'textarea' => [
			'placeholder' => true,
			'cols'        => true,
			'rows'        => true,
			'disabled'    => true,
			'name'        => true,
			'id'          => true,
			'readonly'    => true,
			'required'    => true,
			'autofocus'   => true,
			'form'        => true,
			'wrap'        => true,
		],
		'picture'  => true,
		'source'   => [
			'sizes'  => true,
			'src'    => true,
			'srcset' => true,
			'type'   => true,
			'media'  => true,
		],
	] );

	if ( $extra_attrs ) {
		// Extract the key for comparison.
		$extra_attrs_keys = array_keys( $extra_attrs );

		foreach ( $tags_input_included as $tag => $attrs ) {
			// It is a tag where we want to insert additional attributes?
			if ( in_array( $tag, $extra_attrs_keys, true ) ) {
				// If so, include the extra attributes list within the main tags input list.
				$tags_input_included[ $tag ] = array_merge( $tags_input_included[ $tag ], $extra_attrs[ $tag ] );
			}
		}
	}

	// Form attributes.
	$tags_input_included['form'] = array_merge( $tags_input_included['form'], [ 'novalidate' => true ] );
	// Fieldset attributes.
	// WordPress have an empty array.
	$tags_input_included['fieldset'] = array_merge( $tags_input_included['fieldset'], [
		'id'    => true,
		'class' => true,
		'form'  => true,
		'name'  => true,
	] );

	return wp_kses( $data, $tags_input_included );
}

/**
 * Get Post type by Request
 *
 * @since 1.0.0
 *
 * @return string The post type from $_REQUEST or empty string if not set.
 */
function post_type_name_by_request() {

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

	$post_type = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_STRING );

	return ( $post_type ?: '' );
}

/**
 * Set Unique Term Meta
 *
 * This function store a new term meta if not exists by adding the flag
 * for the unique value. Allow us to not add multiple meta keys with the same key value.
 *
 * @since 1.0.0
 *
 * @param \WP_Term $term  The instance of the term for which set the meta.
 * @param string   $meta  The meta key value.
 * @param mixed    $value The value for the meta.
 *
 * @return mixed Whatever the *_term_meta function returns
 */
function set_unique_term_meta( \WP_Term $term, $meta, $value ) {

	if ( ! get_term_meta( $term->term_id, $meta ) ) {
		return add_term_meta( $term->term_id, $meta, $value, true );
	}

	return update_term_meta( $term->term_id, $meta, $value );
}

/**
 * Retrieve the username
 *
 * @since 1.0.0
 *
 * @param \WP_User $user The user instance from which retrieve the username.
 *
 * @return string The username
 */
function username( \WP_User $user ) {

	$firstname = $user->first_name ?: '';
	$lastname  = $user->last_name ?: '';

	if ( $firstname && $lastname ) {
		return $firstname . ' ' . $lastname;
	}

	$username = $user->display_name ?: '';

	if ( ! $username ) {
		$username = $user->user_nicename ?: '';
	}

	return $username;
}

/**
 * Get Current URL
 *
 * @since 1.0.0
 *
 * @return string The current url
 */
function current_url() {

	$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); // phpcs:ignore
	$current_url = remove_query_arg( 'paged', $current_url );

	return $current_url;
}

/**
 * Is the plugin active for network
 *
 * @since 1.0.0
 *
 * @return bool True if the plugin is active in the network, false otherwise.
 */
function is_plugin_active_for_network() {

	static $plugin = null;

	if ( null === $plugin ) {
		$plugin = new Plugin();
	}

	// May be the function doesn't exists if called during the plugin bootstrap.
	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	return \is_plugin_active_for_network( $plugin->plugin_file() );
}
