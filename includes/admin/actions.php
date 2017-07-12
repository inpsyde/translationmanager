<?php

function tmwp_action_project_add_translation( $arguments ) {
	// defaults
	$request = wp_parse_args(
		$arguments,
		array(
			'tmwp_language'   => tmwp_get_languages_ids(),
			'tmwp_project_id' => null,
		)
	);

	$handler = new \Tmwp\Admin\Handler\Project_Handler();

	$project = (int) $request['tmwp_project_id'];

	if ( ! $project ) {
		$project = $handler->create_project(
			sprintf( __( 'Project %s', 'tmwp' ), date( 'Y-m-d H:i:s' ) )
		);
	}

	// Remember the last manipulated project.
	update_user_meta( get_current_user_id(), 'tmwp_project_recent', $project );

	// Iterate translations
	foreach ( $request['tmwp_language'] as $lang_id ) {
		$handler->add_translation( $project, (int) $request['post_ID'], $lang_id );
	}

	return $project;
}

function _tmwp_handle_actions() {
    if ( ! $_POST ) {
		// Nothing submitted so we stop processing.
        return;
	}

	if ( isset( $_POST[ TMWP_ACTION_PROJECT_ORDER ] ) ) {
		$term = get_term_by( 'slug', $_POST['_tmwp_project_id'], TMWP_TAX_PROJECT );

		_tmwp_project_order( $term );

		wp_redirect(
			get_admin_url(
				null,
				'edit.php?' .
				http_build_query(
					array(
						TMWP_TAX_PROJECT => $_POST['_tmwp_project_id'],
						'post_type'        => TMWP_CART,
					)
				)
			)
		);

		wp_die( '', '', array( 'response' => 302 ) );
	}

	if ( isset( $_POST[ TMWP_ACTION_PROJECT_UPDATE ] ) ) {
		$term = get_term_by( 'slug', $_POST['_tmwp_project_id'], TMWP_TAX_PROJECT );

		_tmwp_project_update( $term );

		wp_redirect(
			get_admin_url(
				null,
				'edit.php?' .
				http_build_query(
					array(
						TMWP_TAX_PROJECT => $_POST['_tmwp_project_id'],
						'post_type'        => TMWP_CART,
					)
				)
			)
		);

		wp_die( '', '', array( 'response' => 302 ) );
	}

	if ( isset( $_POST[ TMWP_ACTION_PROJECT_ADD_TRANSLATION ] ) ) {
		$project = tmwp_action_project_add_translation( $_POST );

		wp_redirect(
			get_admin_url(
				null,
				'edit.php?' .
				http_build_query(
					array(
						TMWP_TAX_PROJECT => get_term_field( 'slug', $project ),
						'post_type'        => TMWP_CART,
						'updated'          => - 1,
					)
				)
			)
		);

		wp_die( '', '', array( 'response' => 302 ) );
	}
}

/**
 * Fetch all project items.
 *
 * @param int $term_id
 *
 * @return array
 */
function tmwp_get_project_items( $term_id ) {
	$get_posts = get_posts(
		array(
			'post_type'      => TMWP_CART,
			'tax_query'      => array(
				array(
					'taxonomy' => TMWP_TAX_PROJECT,
					'field'    => 'id',
					'terms'    => $term_id
				)
			),
			'posts_per_page' => - 1,
			'post_status'    => array('draft', 'published'),
		)
	);

	if ( ! $get_posts || is_wp_error( $get_posts ) ) {
		return array();
	}

	return (array) apply_filters( 'tmwp_get_project_items', $get_posts );
}

/**
 * @param \WP_Term $project_term
 */
function _tmwp_project_order( $project_term ) {
	global $wp_version;

	$project_id = tmwp_api()->project()->create(
		new \Tmwp\Domain\Project(
			'WordPress',
			$wp_version,
			'tmwp',
			TMWP_VERSION
		)
	);

	if ( ! $project_id ) {
		return;
	}

	$languages = tmwp_get_languages();
	foreach ( tmwp_get_project_items( $project_term->term_id ) as $post ) {
		if ( ! $post->_tmwp_post_id || ! isset( $languages[ $post->_tmwp_target_id ] ) ) {
			// Invalid state, try next one.
			continue;
		}

		$source            = get_post( $post->_tmwp_post_id );
		$current           = $source->to_array();
		$current['__meta'] = array(
			'target_language' => $languages[ $post->_tmwp_target_id ]->get_lang_code(),
			'target_id' => $post->_tmwp_target_id
		);

		/**
		 * Filter to update translation data.
		 *
		 * @param array    $current Current data that will be transfered to the API.
		 * @param \WP_Post $source  Post that is currently extracted data from.
		 * @param int      $blog_id ID of the current running blog.
		 *
		 * @return array
		 */
		$data = (array) apply_filters( TMWP_SANITIZE_POST, $current, $source, get_current_blog_id() );

		tmwp_api()->project_item()->create( $project_id, $data );
	}

	update_term_meta( $project_term->term_id, '_tmwp_order_id', $project_id );
}

/**
 * @param \WP_Term $project_term
 */
function _tmwp_project_update( $project_term ) {
	$project_id = get_term_meta( $project_term->term_id, '_tmwp_order_id', true );

	if ( ! $project_id ) {
		// ID missing.
		return;
	}

	$data = tmwp_api()->project()->get( $project_id );

	foreach ( $data['items'] as $item ) {
		do_action( 'tmwp_api_translation_update', $item );
	}
}

// Fetch post save actions which are used for project creation.
add_action( 'load-post.php', '_tmwp_handle_actions' );
add_action( 'load-edit.php', '_tmwp_handle_actions' );

add_action( 'admin_post_tmwp_order_or_update_projects', '_tmwp_handle_actions' );