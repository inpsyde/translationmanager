<?php

namespace Tm4mlp\Admin\Handler;

class Project_Handler {
	public function create_project( $title ) {
		// Check if project already exists.
		$ids = term_exists( $title, TM4MLP_TAX_PROJECT );

		if ( ! $ids ) {
			// Create if it does not exists.
			$ids = wp_insert_term( $title, TM4MLP_TAX_PROJECT );
		}

		if ( is_wp_error( $ids ) ) {
			throw new \Exception( $ids->get_error_message() );
		}

		return $ids['term_id'];
	}

	public function add_translation( $project, $post_id, $lang_code ) {
		$translation_id = wp_insert_post(
			array(
				'post_type'  => TM4MLP_CART,
				'post_title' => sprintf(
					__( '%s: "%s"', 'tm4mlp' ),
					get_post_type( $post_id ),
					get_the_title( $post_id )
				),
				'meta_input' => array(
					'lang_code' => $lang_code,
				)
			)
		);

		wp_set_post_terms( $translation_id, array( $project ), TM4MLP_TAX_PROJECT );
	}
}