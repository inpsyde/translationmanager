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

	$get_posts = get_posts(
		array_merge(
			[
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
			],
			$args
		)
	);

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

	$posts = get_posts(
		[
			'post_type'      => 'project_item',
			'post_status'    => 'any',
			'posts_per_page' => - 1,
			'tax_query'      => [
				'taxonomy' => $taxonomy,
				'field'    => 'id',
				'terms'    => $term_id,
			],
		]
	);

	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
}
