<?php

namespace Translationmanager\Functions;

/**
 * Fetch all project items.
 *
 * @since 1.0.0
 *
 * @param int   $term_id The term id from which retrieve the posts.
 * @param array $args    Additional arguments to set to the query. This take precedence.
 *
 * @return array The posts
 */
function get_project_items( $term_id, array $args = [] ) {

	$get_posts = get_posts( array_merge( [
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
	], $args ) );

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
