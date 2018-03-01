<?php

namespace Translationmanager\Admin\Handler;

class Project_Handler {
	public function create_project( $title ) {

		// Check if project already exists.
		$ids = term_exists( $title, 'translationmanager_project' );

		if ( ! $ids ) {
			// Create if it does not exists.
			$ids = wp_insert_term( $title, 'translationmanager_project' );
		}

		if ( is_wp_error( $ids ) ) {
			throw new \Exception( $ids->get_error_message() );
		}

		return (int) $ids['term_id'];
	}

	public function add_translation( $project, $post_id, $lang_id ) {

		$labels = get_post_type_labels( get_post_type_object( get_post_type( $post_id ) ) );

		$translation_id = wp_insert_post(
			[
				'post_type'  => 'project_item',
				'post_title' => sprintf(
					__( '%s: "%s"', 'translationmanager' ),
					esc_html( $labels->singular_name ),
					get_the_title( $post_id )
				),
				'meta_input' => [
					'_translationmanager_target_id' => $lang_id,
					'_translationmanager_post_id'   => $post_id,
				],
			]
		);

		wp_set_post_terms( $translation_id, [ $project ], 'translationmanager_project' );
	}
}