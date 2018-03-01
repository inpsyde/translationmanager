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

	wp_safe_redirect( get_admin_url( $blog_id, $page . http_build_query( $args ) ), $status );

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
	$tags_input_included['form'] = array_merge( $tags_input_included['form'], array( 'novalidate' => true ) );
	// Fieldset attributes.
	// WordPress have an empty array.
	$tags_input_included['fieldset'] = array_merge( $tags_input_included['fieldset'], array(
		'id'    => true,
		'class' => true,
		'form'  => true,
		'name'  => true,
	) );

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
