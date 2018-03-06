<?php

namespace Translationmanager\Functions;

use Translationmanager\Plugin;

/**
 * Register Cart Post Type
 *
 * @todo  Create class to manage cpt project filters. See ProjectItem
 *
 * @since 1.0.0
 *
 * @return void
 */
function register_translationmanager_project_posttype() {

	register_post_type( 'project_item', [
		'label'         => esc_html__( 'Project', 'translationmanager' ),
		'labels'        => [
			'name'      => esc_html__( 'Project', 'translationmanager' ),
			'menu_name' => esc_html__( 'Translation', 'translationmanager' ),
		],
		'show_in_menu'  => false,
		'description'   => esc_html__( 'What you are about to order.', 'translationmanager' ),
		'public'        => true,
		'capabilities'  => [
			// Removes support for the "Add New" function ( use 'do_not_allow' / false for multisite set ups ).
			'create_posts' => 'do_not_allow',
		],
		'map_meta_cap'  => true,
		'menu_position' => 100,
		'supports'      => [ 'title' ],
		'menu_icon'     => ( new Plugin() )->url( '/resources/img/tm-icon-bw.png' ),
	] );
}

/**
 * Remove month from Project post type page
 *
 * @todo  Move into the cpt project class. See ProjectItem
 *
 * @since 1.0.0
 *
 * @return void
 */
function project_remove_month() {

	$screen = get_current_screen();

	if ( ! $screen ) {
		return;
	}

	if ( 'project_item' !== $screen->post_type ) {
		return;
	}

	add_filter( 'months_dropdown_results', '__return_empty_array' );
}

/**
 * Change the Bulk Actions labels for projects
 *
 * @todo  Move into the cpt project class. See ProjectItem
 *
 * @since 1.0.0
 *
 * @param array $actions The bulk actions list.
 *
 * @return array The filtered actions
 */
function filter_bulk_actions_labels_for_project( array $actions ) {

	unset( $actions['edit'] );

	if ( isset( $actions['trash'] ) ) {
		$actions['trash'] = esc_html__( 'Remove from project', 'translationmanager' );
	}

	return $actions;
}

/**
 * Filter Row Actions for Project post type
 *
 * @todo  Move into the cpt project class. See ProjectItem
 *
 * @since 1.0.0
 *
 * @param array    $actions The actions list.
 * @param \WP_Post $post    The current post.
 *
 * @return array The filtered list
 */
function filter_row_actions_for_project( array $actions, \WP_Post $post ) {

	if ( 'project_item' !== $post->post_type ) {
		return $actions;
	}

	if ( ! isset( $actions['trash'] ) ) {
		return $actions;
	}

	// Delete/Remove only.
	return [
		'trash' => str_replace(
			'>Trash<',
			'>' . esc_html__( 'Remove from project', 'translationmanager' ) . '<',
			$actions['trash']
		),
	];
}

/**
 * Remove states from Project Post Type
 *
 * @todo  Move into the cpt project class. See ProjectItem
 *
 * @since 1.0.0
 *
 * @param array    $post_states The post states.
 * @param \WP_Post $post        The post object.
 *
 * @return array Empty array if the post type is the project one.
 */
function remove_states_from_project( array $post_states, \WP_Post $post ) {

	if ( 'project_item' !== $post->post_type ) {
		return $post_states;
	}

	return [];
}

/**
 * Project Box in Edit Page
 *
 * @since 1.0.0
 *
 * @param string $value The views link. Untouched.
 *
 * @return string The untouched parameter
 */
function template_project_box_form_in_edit_page( $value ) {

	$slug = sanitize_title( filter_input( INPUT_GET, 'translationmanager_project', FILTER_SANITIZE_STRING ) );

	if ( $slug ) {
		$term = get_term_by( 'slug', $slug, 'translationmanager_project' );
		// This is used inside the view.
		( new \Translationmanager\MetaBox\OrderInfo( $term->term_id ) )->render_template();
	}

	return $value;
}

/**
 * Project Title and Description Form in edit page.
 *
 * @since 1.0.0
 *
 * @param string $value The views link. Untouched.
 *
 * @return string The untouched parameter
 */
function template_project_title_description_form_in_edit_page( $value ) {

	$slug = sanitize_title( filter_input( INPUT_GET, 'translationmanager_project', FILTER_SANITIZE_STRING ) );

	if ( $slug ) {
		$term = get_term_by( 'slug', $slug, 'translationmanager_project' );

		require get_template( '/views/project/form-title-description.php' );
	}

	return $value;
}

/**
 * Filter Bulk Messages
 *
 * @since 1.0.0
 *
 * @param array $bulk_messages The messages list for the post type.
 * @param array $bulk_counts   The quantity of the posts based on states.
 *
 * @return array The filtered bulk messages
 */
function filter_bulk_updated_messages_for_project( array $bulk_messages, array $bulk_counts ) {

	$locked_msg = 1 === $bulk_counts['locked']
		? esc_html__( '1 page not updated, somebody is editing it.', 'translationmanager' )
		: _n( '%s page not updated, somebody is editing it.', '%s pages not updated, somebody is editing them.', $bulk_counts['locked'] );

	$bulk_messages['project_item'] = [
		'updated'   => esc_html__( 'Project has been updated.', 'translationmanager' ),
		'locked'    => $locked_msg,
		'deleted'   => _n(
			'%s translation permanently deleted.',
			'%s translations permanently deleted.',
			$bulk_counts['deleted'],
			'translationmanager'
		),
		'trashed'   => _n(
			'%s translation moved to the Trash.',
			'%s translations moved to the Trash.',
			$bulk_counts['trashed'],
			'translationmanager'
		),
		'untrashed' => _n(
			'%s translation restored from the Trash.',
			'%s translations restored from the Trash.',
			$bulk_counts['untrashed'],
			'translationmanager'
		),
	];

	//	$bulk_messages['project_item'] = array(
	//		'updated'   => _n( '%s translation updated.', '%s translations updated.', $bulk_counts['updated'] ),
	//		'locked'    => _n( '%s translation not updated, somebody is editing it.', '%s translations not updated, somebody is editing them.', $bulk_counts['locked'] ),
	//		'deleted'   => _n( '%s translation permanently deleted.', '%s translations permanently deleted.', $bulk_counts['deleted'] ),
	//		'trashed'   => _n( '%s translation removed from the project.', '%s translations removed from the project.', $bulk_counts['trashed'] ),
	//		'untrashed' => _n( '%s translation restored at the project.', '%s translations restored at the project.', $bulk_counts['untrashed'] ),
	//	);

	$updated = filter_input( INPUT_GET, 'updated', FILTER_SANITIZE_NUMBER_INT );
	if ( - 1 === $updated ) {
		$bulk_messages['project_item']['updated'] = esc_html__( 'Project has been created.', 'translationmanager' );
	}

	return $bulk_messages;
}

/**
 * Fetch all project items.
 *
 * @param int $term_id The term id from which retrieve the posts.
 *
 * @return array The posts
 */
function get_project_items( $term_id ) {

	$get_posts = get_posts( [
		'post_type'      => 'project_item',
		'tax_query'      => [
			[
				'taxonomy' => 'translationmanager_project',
				'field'    => 'id',
				'terms'    => $term_id,
			],
		],
		'posts_per_page' => - 1,
		'post_status'    => [ 'draft', 'published' ],
	] );

	if ( ! $get_posts || is_wp_error( $get_posts ) ) {
		return [];
	}

	/**
	 * Get Project Items
	 *
	 * @since 1.0.0
	 *
	 * @param array $posts The posts.
	 */
	return (array) apply_filters( 'translationmanager_get_project_items', $get_posts );
}

/**
 * Delete all post project based on term ID
 *
 * @since 1.0.0
 *
 * @param int $term_id The ID of the term related to the projects. Used to retrieve the taxonomy.
 *
 * @return void
 */
function delete_all_projects_posts_based_on_project_taxonomy_term( $term_id ) {

	$term     = get_term( $term_id );
	$taxonomy = is_array( $term->taxonomy ) ? $term->taxonomy[0] : $term->taxonomy;

	if ( is_wp_error( $term ) || 'translationmanager_project' !== $taxonomy ) {
		return;
	}

	$posts = get_posts( [
		'post_type'      => 'project_item',
		'post_status'    => 'any',
		'posts_per_page' => - 1,
		'tax_query'      => [
			'taxonomy' => $taxonomy,
			'field'    => 'id',
			'terms'    => $term_id,
		],
	] );

	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
}

//function tmanager_cart_footer( $which ) {
//	if ( 'edit-tmanager_cart' !== get_current_screen()->id ) {
//		return;
//	}
//
//	global $wp_query;
//	if ( $wp_query->post_count <= 0 ) {
//		return;
//	}
//
//	$request = wp_parse_args(
//		$_GET,  // Input var ok.
//		array(
//			'translationmanager_project' => null,
//		)
//	);
//
//	if ( isset( $request['translationmanager_project'] ) && $_GET['translationmanager_project'] ) {
//		$current_slug = $_GET['translationmanager_project']; // Input var ok.
//		$term         = get_term_by( 'slug', $current_slug, 'translationmanager_project' );
//
//		if ( ! is_wp_error( $term )
//		     && get_term_meta( $term->term_id, '_translationmanager_order_id' )
//		) {
//			// This has an order id so we show the update button.
//			require Functions\get_template( 'views/cart/extra-tablenav-update.php' );
//			_e( 'Thanks for your order.', 'translationmanager' );
//
//			return;
//		}
//	}
//
//	require Functions\get_template( '/views/cart/extra-tablenav.php' );
//}
//
//add_action( 'manage_posts_extra_tablenav', 'tmanager_cart_footer' );
