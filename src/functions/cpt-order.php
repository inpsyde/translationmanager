<?php

namespace Translationmanager\Functions;

/**
 * Register Order Post Type
 *
 * @todo  Create class to manage cpt order filters.
 *
 * @since 1.0.0
 *
 * @return void
 */
function register_translationmanager_order_posttype() {

	register_post_type(
		'tm_order',
		[
			'capabilities' => [
				// Removes support for the "Add New" function ( use 'do_not_allow' / false for multisite set ups ).
				'create_posts' => false,
			],
			'description'  => esc_html__( 'Your placed orders.', 'translationmanager' ),
			'hierarchical' => true,
			'label'        => esc_html__( 'Order', 'translationmanager' ),
			'map_meta_cap' => true,
			'public'       => true,
			'show_in_menu' => 'edit.php?post_type=translationmanager_trans_status',
			'supports'     => [ 'title' ],
		]
	);
}

/**
 * Remove month select from Order post type
 *
 * @todo  Move into the order cpt class.
 *
 * @since 1.0.0
 *
 * @return void
 */
function order_remove_month() {

	$screen = get_current_screen();

	if ( ! $screen ) {
		return;
	}

	if ( 'tm_order' !== $screen->post_type ) {
		return;
	}

	// Remove all WordPress basics as this post type is not meant to be maintained by users.
	remove_meta_box( 'submitdiv', 'translationmanager_order', 'side' );
	remove_meta_box( 'slugdiv', 'translationmanager_order', 'normal' );

	add_filter( 'months_dropdown_results', '__return_empty_array' );
}

/**
 * Filter Bulk actions for Order post type
 *
 * @todo  Move into the order cpt class.
 *
 * @since 1.0.0
 *
 * @param array $actions The list of the bulk actions available.
 *
 * @return array The filtered list
 */
function filter_bulk_actions_for_order( array $actions ) {

	unset(
		$actions['edit'],
		$actions['trash']
	);

	return $actions;
}

/**
 * Order items have no trash.
 *
 * @todo  Move into the order cpt class.
 *
 * @since 1.0.0
 *
 * @param int $post_id The ID of the post currently trashed.
 *
 * @return void
 */
function delete_post_order_on_trashing( $post_id ) {

	if ( 'tm_order' === get_post_type( $post_id ) ) {
		wp_delete_post( $post_id );
	}
}

/**
 * Filter Row Actions for Order post type
 *
 * @todo  Move into the order cpt class.
 *
 * @since 1.0.0
 *
 * @param array    $actions The actions to filter.
 * @param \WP_Post $post    The post type for which filter the actions.
 *
 * @return array The filtered actions
 */
function filter_row_actions_for_order( array $actions, \WP_Post $post ) {

	if ( 'tm_order' === $post->post_type ) {
		$actions = [ 'a' => 'b' ];
	}

	return $actions;
}
