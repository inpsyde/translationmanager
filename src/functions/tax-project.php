<?php

namespace Translationmanager\Functions;

use Translationmanager\Taxonomy;
use Translationmanager\Admin;

/**
 * Register the Project Taxonomy
 *
 * @todo  Create a class for the taxonomy. See Project
 *
 * @since 1.0.0
 *
 * @return void
 */
function register_projects_taxonomy() {

	register_taxonomy(
		'translationmanager_project',
		'tm_cart',
		array(
			'label'  => esc_html__( 'Projects', 'translationmanager' ),
			'labels' => array(
				'add_new_item' => esc_html__( 'Create new project', 'translationmanager' ),
			),
			'public' => true,
		)
	);
}

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
 * Save Project Info based on request
 *
 * @since 1.0.0
 *
 * @return void
 */
function project_info_save() {

	$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );
	if ( 'translationmanager_project_info_save' !== $action ) {
		return;
	}

	$project_id = sanitize_title( filter_input( INPUT_POST, '_translationmanager_project_id', FILTER_SANITIZE_STRING ) );
	if ( ! $project_id ) {
		return;
	}

	$term = get_term_by( 'slug', $project_id, 'translationmanager_project' );

	$update = wp_update_term( $term->term_id, 'translationmanager_project', [
		'name'        => sanitize_text_field( filter_input( INPUT_POST, 'tag-name', FILTER_SANITIZE_STRING ) ),
		'description' => filter_input( INPUT_POST, 'description', FILTER_SANITIZE_STRING ),
	] );

	if ( is_wp_error( $update ) ) {
		wp_die( esc_html__( 'Something went wrong. Please go back and try again.', 'translationmanager' ) );
	}

	wp_safe_redirect( wp_get_referer() );

	die;
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
	$handler   = new Admin\Handler\Project_Handler();

	// Be sure we have only valid elements.
	$languages = array_filter( $languages );

	if ( 'bulk_translate' !== $action || empty( $post_ids ) || ! $languages ) {
		return $redirect_to;
	}

	if ( - 1 === $project ) {
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
