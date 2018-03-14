<?php
/**
 * Project Handler
 *
 * @since   1.0.0
 * @package Translationmanager
 */

namespace Translationmanager;

/**
 * Class ProjectHandler
 *
 * @since   1.0.0
 * @package Translationmanager
 */
class ProjectHandler {
	/**
	 * Create Project
	 *
	 * @since 1.0.0
	 *
	 * @param string $title The title for the project.
	 *
	 * @throws \Exception In case the project cannot be created.
	 *
	 * @return int
	 */
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

	/**
	 * Add Translation
	 *
	 * @since 1.0.0
	 *
	 * @param string $project The project name item.
	 * @param int    $post_id The post associated to this project item.
	 * @param int    $lang_id The language id of the project item.
	 */
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

	/**
	 * Create new Project by Date
	 *
	 * @since 1.0.0
	 *
	 * @throws \Exception In case the project cannot be created.
	 *
	 * @return int The new project ID
	 */
	public static function create_project_using_date() {

		return ( new self() )->create_project(
			sprintf( esc_html__( 'Project %s', 'translationmanager' ), date( 'Y-m-d H:i:s' ) )
		);
	}
}
