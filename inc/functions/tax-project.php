<?php

namespace Translationmanager\Functions;

use Translationmanager\Notice\TransientNoticeService;
use Translationmanager\ProjectHandler;
use Translationmanager\Project;

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
    /** @var array<array-key, mixed> $languages */
    $languages = \filter_input( INPUT_GET, 'translationmanager_bulk_languages', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );
	$project   = \filter_input( INPUT_GET, 'translationmanager_project_id', FILTER_SANITIZE_NUMBER_INT );
	$handler   = new \Translationmanager\ProjectHandler();

	// Do not perform anything if project hasn't been sent.
	if ( ! $project ) {
		TransientNoticeService::add_notice(
			esc_html__( 'You must select a project in order to translate items.', 'translationmanager' ),
			'warning'
		);

		return wp_get_referer();
	}

	// Be sure we have only valid elements.
	$languages = array_filter( $languages );

	if ( 'bulk_translate' !== $action || empty( $post_ids ) || ! $languages ) {
		return $redirect_to;
	}

	// Isn't a number, don't try to convert to number -1.
	try {
		if ( '-1' === $project ) {
			$project = ProjectHandler::create_project_using_date();
		}
	} catch ( \Exception $e ) {
		TransientNoticeService::add_notice( $e->getMessage(), 'warning' );

		return wp_get_referer();
	}

	// Iterate translations.
	foreach ( $post_ids as $post_id ) {
		foreach ( $languages as $lang_id ) {
			$handler->add_translation( $project, $post_id, $lang_id );
		}
	}

	$redirect_to = Project\Taxonomy::get_project_link( $project );

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
