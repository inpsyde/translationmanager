<?php

function tm4mlp_action_project_add_translation( $arguments ) {
	// defaults
	$request = wp_parse_args(
		$arguments,
		array(
			'tm4mlp_language'   => tm4mlp_get_languages_ids(),
			'tm4mlp_project_id' => null,
		)
	);

	$handler = new \Tm4mlp\Admin\Handler\Project_Handler();

	$project = (int) $request['tm4mlp_project_id'];

	if ( ! $project ) {
		$project = $handler->create_project(
			sprintf( __( 'Project %s', 'tm4mlp' ), date( 'Y-m-d H:i:s' ) )
		);
	}

	// Remember the last manipulated project.
	update_user_meta( get_current_user_id(), 'tm4mlp_project_recent', $project );

	// Iterate translations
	foreach ( $request['tm4mlp_language'] as $lang_id ) {
		$handler->add_translation( $project, (int) $request['post_ID'], $lang_id );
	}

	return $project;
}

function _tm4mlp_handle_actions() {
	if ( ! $_POST ) {
		// Nothing submitted so we stop processing.
	}

	if ( isset( $_GET[ TM4MLP_ACTION_PROJECT_ORDER ] ) ) {
		$term = get_term_by( 'slug', $_GET['_tm4mlp_project_id'], TM4MLP_TAX_PROJECT );

		_tm4mlp_project_order( $term );

		wp_redirect(
			get_admin_url(
				null,
				'edit.php?' .
				http_build_query(
					array(
						TM4MLP_TAX_PROJECT => $_GET['_tm4mlp_project_id'],
						'post_type'        => TM4MLP_CART,
					)
				)
			)
		);

		wp_die( '', '', array( 'response' => 302 ) );
	}

	if ( isset( $_POST[ TM4MLP_ACTION_PROJECT_ADD_TRANSLATION ] ) ) {
		$project = tm4mlp_action_project_add_translation( $_POST );

		wp_redirect(
			get_admin_url(
				null,
				'edit.php?' .
				http_build_query(
					array(
						TM4MLP_TAX_PROJECT => get_term_field( 'slug', $project ),
						'post_type'        => TM4MLP_CART,
						'updated'          => - 1,
					)
				)
			)
		);

		wp_die( '', '', array( 'response' => 302 ) );
	}

}

/**
 * @param \WP_Term T $project_term
 */
function _tm4mlp_project_order( $project_term ) {
	$posts = get_posts( array(
		'post_type'      => TM4MLP_CART,
		'tax_query'      => array(
			'taxonomy' => TM4MLP_TAX_PROJECT,
			'field'    => 'id',
			'terms'    => $project_term->term_id
		),
		'posts_per_page' => - 1,
		'post_status'    => get_post_stati(),
	) );

	// TODO What if no post in list?

	$request   = array();
	$languages = tm4mlp_get_languages();
	foreach ( $posts as $post ) {
		$lang_id = get_post_meta( $post->ID, '_tm4mlp_target_id', true );

		if ( null === $lang_id ) {
			// Invalid.
			continue;
		}

		if ( ! $lang_id || ! isset( $languages[ $lang_id ] ) ) {
			// TODO error handling.
			continue;
		}

		$source_id = get_post_meta( $post->ID, '_tm4mlp_post_id', true );

		if ( ! $source_id ) {
			// invalid.
			continue;
		}

		$current = array(
			'__meta' => array(
				'target_language' => $languages[ $lang_id ]->get_lang_code(),
			),
		);

		$source  = get_post( $source_id );
		$current = array_merge( $source->to_array(), $current );

		$request[] = tm4mlp_sanitize_post( $current, $source );
	}

	$project_id = tm4mlp_api_project_create( $request );

	update_term_meta( $project_term->term_id, '_tm4mlp_order_id', $project_id );
}

add_action( 'load-post.php', '_tm4mlp_handle_actions' );
add_action( 'load-edit.php', '_tm4mlp_handle_actions' );