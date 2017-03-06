<?php

function tm4mlp_handle_load_post() {
	if ( ! $_POST ) {
		// Nothing submitted so we stop processing.
	}

	if ( isset( $_POST[ TM4MLP_ACTION_PROJECT_ADD_TRANSLATION ] ) ) {
		$handler = new \Tm4mlp\Admin\Handler\Project_Handler();

		$project = $handler->create_project(
			sprintf( __( 'Project %s', 'tm4mlp' ), date( 'Y-m-d H:i:s' ) )
		);

		$handler->add_translation( $project, (int) $_POST['post_ID'], 'fr_FR' ); // Input var ok.

		wp_redirect(
			get_admin_url(
				null,
				'edit.php?' .
				http_build_query(
					array(
						TM4MLP_TAX_PROJECT => get_term_field( 'slug', $project ),
						'post_type'        => TM4MLP_CART,
					)
				)
			)
		);

		wp_die( '', '', array( 'response' => 302 ) );
	}
}

add_action( 'load-post.php', 'tm4mlp_handle_load_post' );