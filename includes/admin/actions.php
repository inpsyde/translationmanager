<?php

function tm4mlp_action_project_add_translation( $arguments ) {
	// defaults
	$request = wp_parse_args(
		$arguments,
		array(
			'tm4mlp_language' => tm4mlp_get_languages_ids(),
		)
	);

	$handler = new \Tm4mlp\Admin\Handler\Project_Handler();

	$project = $handler->create_project(
		sprintf( __( 'Project %s', 'tm4mlp' ), date( 'Y-m-d H:i:s' ) )
	);

	// Iterate translations
	$languages = tm4mlp_get_languages();
	foreach ( $request['tm4mlp_language'] as $lang_id ) {
		$handler->add_translation( $project, (int) $request['post_ID'], $languages[ $lang_id ]->get_lang_code() );
	}

	return $project;
}

function _tm4mlp_handle_load_post() {
	if ( ! $_POST ) {
		// Nothing submitted so we stop processing.
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

add_action( 'load-post.php', '_tm4mlp_handle_load_post' );