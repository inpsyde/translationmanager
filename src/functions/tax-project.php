<?php

namespace Translationmanager\Functions;

use Translationmanager\Taxonomy;

/**
 * Hide Term Slug Wrap
 *
 * @todo  Create unique css for the entire plugin to register and load when request.
 *
 * @since 1.0.0
 *
 * @return void
 */
function project_hide_slug() {

	?>
	<style>
		.form-field.term-slug-wrap, input[name=slug], span.title {
			display: none;
		}
	</style>
	<?php
}

/**
 * Edit Term Link for Project Taxonomy
 *
 * @todo  Move into the taxonomy class?
 *
 * @since 1.0.0
 *
 * @param string $location The location link.
 * @param int    $term_id  The term id.
 * @param string $taxonomy The taxonomy name associated to the term.
 *
 * @return string The filtered location
 */
function edit_term_link_for_project_taxonomy( $location, $term_id, $taxonomy ) {

	if ( 'translationmanager_project' === $taxonomy ) {
		$location = Taxonomy\Project::get_project_link( $term_id );
	}

	return $location;
}

/**
 * Bulk translate project
 *
 * @throws \Exception If isn't possible to create a project.
 *
 * @param string $redirect_to The redirect to string.
 * @param string $action      The currently action to take.
 * @param array  $post_ids    The posts ids list.
 *
 * @return string The redirect_to value
 */
function bulk_translate_projects_by_request_posts( $redirect_to, $action, $post_ids ) {

	$languages = filter_input( INPUT_GET, 'translationmanager_bulk_languages', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
	$project   = filter_input( INPUT_GET, 'translationmanager-projects', FILTER_SANITIZE_NUMBER_INT );
	$handler   = new \Translationmanager\ProjectHandler();

	// Be sure we have only valid elements.
	$languages = array_filter( $languages );

	if ( 'bulk_translate' !== $action || empty( $post_ids ) || ! $languages ) {
		return $redirect_to;
	}

	// Isn't a number, don't try to convert to number -1.
	if ( '-1' === $project ) {
		$project = (int) $handler->create_project(
			sprintf(
				esc_html__( 'Project %s', 'translationmanager' ),
				date( 'Y-m-d H:i:s' )
			)
		);
	}

	// Iterate translations.
	foreach ( $post_ids as $post_id ) {
		foreach ( $languages as $lang_id ) {
			$handler->add_translation( $project, $post_id, $lang_id );
		}
	}

	$redirect_to = Taxonomy\Project::get_project_link( $project );

	return $redirect_to;
}

/**
 * Retrieve Projects
 *
 * @since 1.0.0
 *
 * @return array A list of projects.
 */
function projects() {

	$terms = get_terms(
		[
			'taxonomy'   => 'translationmanager_project',
			'hide_empty' => false,
			'meta_query' => [
				[
					'key'     => '_translationmanager_order_id',
					'compare' => 'NOT EXISTS',
					'value'   => '',
				],
			],
		]
	);

	$projects = [];
	foreach ( $terms as $term ) {
		$projects[ $term->term_id ] = $term->name;
	}

	return $projects;
}
