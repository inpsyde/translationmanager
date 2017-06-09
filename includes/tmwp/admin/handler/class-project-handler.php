<?php

namespace Tmwp\Admin\Handler;

class Project_Handler {
	public function create_project( $title ) {
		// Check if project already exists.
		$ids = term_exists( $title, TMWP_TAX_PROJECT );

		if ( ! $ids ) {
			// Create if it does not exists.
			$ids = wp_insert_term( $title, TMWP_TAX_PROJECT );
		}

		if ( is_wp_error( $ids ) ) {
			throw new \Exception( $ids->get_error_message() );
		}

		return (int) $ids['term_id'];
	}

	public function add_translation( $project, $post_id, $lang_id ) {
		$labels = get_post_type_labels( get_post_type_object( get_post_type( $post_id ) ) );

		$translation_id = wp_insert_post(
			array(
				'post_type'  => TMWP_CART,
				'post_title' => sprintf(
					__( '%s: "%s"', 'tmwp' ),
					$labels->singular_name,
					get_the_title( $post_id )
				),
				'meta_input' => array(
					'_tmwp_target_id' => $lang_id,
					'_tmwp_post_id'   => $post_id,
				)
			)
		);

		wp_set_post_terms( $translation_id, array( $project ), TMWP_TAX_PROJECT );
	}
}