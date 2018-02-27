<?php

/**
 * @param $arguments
 *
 * @return bool|int
 */
function translationmanager_action_project_add_translation( $arguments ) {

	// defaults
	$request = wp_parse_args(
		$arguments,
		array(
			'translationmanager_language'   => translationmanager_get_languages_ids(),
			'translationmanager_project_id' => null,
		)
	);

	$handler = new \Translationmanager\Admin\Handler\Project_Handler();

	$project = (int) $request['translationmanager_project_id'];

	if ( ! $project ) {
		$project = $handler->create_project(
			sprintf( __( 'Project %s', 'translationmanager' ), date( 'Y-m-d H:i:s' ) )
		);
	}

	/**
	 * Runs before adding translations to the cart.
	 *
	 * You might add other things to the cart before the translations kick in
	 * or check against some other things (like account balance) to stop adding things to the cart
	 * and show some error message.
	 *
	 * For those scenarios this filter allows turn it's value into false.
	 * In that case it will neither add things to the project/cart
	 * nor redirect to the project- / cart-view.
	 *
	 * @param bool  $valid     Initially true and can be torn to false to stop adding items to the cart.
	 * @param int   $project   ID of the project (actually a term ID).
	 * @param int   $post_id   ID of the post that will be added to the cart.
	 * @param int[] $languages IDs of the target languages (assoc pair).
	 *
	 * @see wp_insert_post() actions and filter to access each single transation that is added to cart.
	 */
	$valid = apply_filters(
		'translationmanager_filter_before_add_to_project',
		true,
		$project,
		$request['post_ID'],
		$request['translationmanager_language']
	);

	if ( true !== $valid ) {
		return false;
	}

	// Remember the last manipulated project.
	update_user_meta( get_current_user_id(), 'translationmanager_project_recent', $project );

	// Iterate translations
	foreach ( $request['translationmanager_language'] as $lang_id ) {
		$handler->add_translation( $project, (int) $request['post_ID'], $lang_id );
	}

	/**
	 * Filter the output of the `translationmanager_action_project_add_translation` function.
	 *
	 * After adding posts to a project / cart it will redirect to this project.
	 * One last time you can filter to which project it will redirect (by using the ID)
	 * or if should'nt redirect at all (by setting the value to "false").
	 *
	 * @param int   $project   ID of the project (actually a term ID).
	 * @param int   $post_id   ID of the post that will be added to the cart.
	 * @param int[] $languages IDs of the target languages (assoc pair).
	 *
	 * @see translationmanager_action_project_add_translation() where this filter resides.
	 * @see translationmanager_get_languages() how languages are gathered.
	 */
	return apply_filters(
		'translationmanager_action_project_add_translation',
		$project,
		$request['post_ID'],
		$request['translationmanager_language']
	);
}

function _translationmanager_handle_actions() {

	$post_data = filter_input_array(
		INPUT_POST,
		array(
			'translationmanager_action_project_order'           => FILTER_SANITIZE_STRING,
			'translationmanager_action_project_update'          => FILTER_SANITIZE_STRING,
			'translationmanager_action_project_add_translation' => FILTER_SANITIZE_STRING,
			'_translationmanager_project_id'                    => FILTER_SANITIZE_STRING,
			'translationmanager_project_id'                     => FILTER_SANITIZE_NUMBER_INT,
			'post_ID'                                           => FILTER_SANITIZE_NUMBER_INT,
			'translationmanager_language'                       => array(
				'filter' => FILTER_SANITIZE_STRING,
				'flags'  => FILTER_FORCE_ARRAY,
			),
		)
	);

	// If nothing submitted or no action detected we stop processing.
	if (
		! $post_data
		|| (
			null === $post_data['translationmanager_action_project_order']
			&& null === $post_data['translationmanager_action_project_update']
			&& null === $post_data['translationmanager_action_project_add_translation']
		)
	) {
		return;
	}

	if ( null !== $post_data['translationmanager_action_project_order'] ) {
		$term = get_term_by( 'slug', $post_data['_translationmanager_project_id'], 'translationmanager_project' );

		_translationmanager_project_order( $term );

		wp_redirect(
			get_admin_url(
				null,
				'edit.php?' .
				http_build_query(
					array(
						'translationmanager_project' => $post_data['_translationmanager_project_id'],
						'post_type'                  => 'tmanager_cart',
					)
				)
			)
		);

		wp_die( '', '', array( 'response' => 302 ) );
	}

	if ( null !== $post_data['translationmanager_action_project_update'] ) {
		$term = get_term_by( 'slug', $post_data['_translationmanager_project_id'], 'translationmanager_project' );

		_translationmanager_project_update( $term );

		wp_redirect(
			get_admin_url(
				null,
				'edit.php?' .
				http_build_query(
					array(
						'translationmanager_project' => $post_data['_translationmanager_project_id'],
						'post_type'                  => 'tmanager_cart',
					)
				)
			)
		);

		wp_die( '', '', array( 'response' => 302 ) );
	}

	if ( null !== $post_data['translationmanager_action_project_add_translation'] ) {

		$updater = new \Translationmanager\Admin\Cart_Updater();
		$updater->setup();

		$project = translationmanager_action_project_add_translation(
			array(
				'translationmanager_language'   => $post_data['translationmanager_language'],
				'translationmanager_project_id' => $post_data['translationmanager_project_id'],
				'post_ID'                       => $post_data['post_ID'],
			)
		);

		if ( false === $project ) {
			// Project has been invalidated so we don't redirect there.
			return;
		}

		wp_redirect(
			get_admin_url(
				null,
				'edit.php?' .
				http_build_query(
					array(
						'translationmanager_project' => get_term_field( 'slug', $project ),
						'post_type'                  => 'tmanager_cart',
						'updated'                    => - 1,
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
function translationmanager_get_project_items( $term_id ) {

	$get_posts = get_posts(
		array(
			'post_type'      => 'tmanager_cart',
			'tax_query'      => array(
				array(
					'taxonomy' => 'translationmanager_project',
					'field'    => 'id',
					'terms'    => $term_id,
				),
			),
			'posts_per_page' => - 1,
			'post_status'    => array( 'draft', 'published' ),
		)
	);

	if ( ! $get_posts || is_wp_error( $get_posts ) ) {
		return array();
	}

	return (array) apply_filters( 'translationmanager_get_project_items', $get_posts );
}

/**
 * @param \WP_Term $project_term
 */
function _translationmanager_project_order( $project_term ) {

	global $wp_version;

	$project_id = translationmanager_api()->project()->create(
		new \Translationmanager\Domain\Project(
			'WordPress',
			$wp_version,
			'translationmanager',
			TRANSLATIONMANAGER_VERSION,
			$project_term->name
		)
	);

	if ( ! $project_id ) {
		return;
	}

	// posts get collected by post type
	$post_types = array();

	$languages = translationmanager_get_languages();
	foreach ( translationmanager_get_project_items( $project_term->term_id ) as $post ) {
		if ( ! $post->_translationmanager_post_id || ! isset( $languages[ $post->_translationmanager_target_id ] ) ) {
			// Invalid state, try next one.
			continue;
		}

		$source_post = get_post( $post->_translationmanager_post_id );
		if ( ! $source_post ) {
			continue;
		}

		$source_site_id = get_current_blog_id();

		$data = \Translationmanager\Translation_Data::for_outgoing(
			$source_post,
			$source_site_id,
			$post->_translationmanager_target_id,
			$languages[ $post->_translationmanager_target_id ]->get_lang_code()
		);

		/**
		 * Fires before translation data is transfered to the API.
		 *
		 * Data can be edited in place by listeners.
		 *
		 * @param \Translationmanager\Translation_Data $data
		 */
		do_action_ref_array( 'translationmanager_outgoing_data', array( $data ) );
		$post_types[ $languages[ $post->_translationmanager_target_id ]->get_lang_code() ][ $source_post->post_type ][] = $data->to_array();
	}

	foreach ( $post_types as $post_type_target_language => $post_types_data ) {
		foreach ( $post_types_data as $post_type_name => $post_type_content ) {
			translationmanager_api()
				->project_item()
				->create( $project_id, $post_type_name, $post_type_target_language, $post_type_content );
		}
	}

	update_term_meta( $project_term->term_id, '_tmanager_order_id', $project_id );
}

/**
 * @param \WP_Term $project_term
 */
function _translationmanager_project_update( $project_term ) {

	$project_id = get_term_meta( $project_term->term_id, '_tmanager_order_id', true );

	if ( ! $project_id ) {
		// ID missing.
		return;
	}

	$translation_data = translationmanager_api()->project()->get( $project_id );

	foreach ( $translation_data['items'] as $items ) {
		foreach ( $items['data'] as $item ) {
			$translation = \Translationmanager\Translation_Data::for_incoming( (array) $item );

			/**
			 * Fires for each item or translation received from the API.
			 *
			 * @param \Translationmanager\Translation_Data $translation Translation data built from data received from API
			 */
			do_action( 'translationmanager_incoming_data', $translation );

			/**
			 * Filters the updater that executed have to return the updated post
			 */
			$updater = apply_filters( 'translationmanager_post_updater', null, $translation );

			$post = is_callable( $updater ) ? $updater( $translation ) : null;

			if ( $post instanceof \WP_Post ) {

				/**
				 * Fires after the updater has updated the post.
				 *
				 * @param \WP_Post                             $post        Just updated post
				 * @param \Translationmanager\Translation_Data $translation Translation data built from data received from API
				 */
				do_action( 'translationmanager_updated_post', $post, $translation );
			}
		}
	}
}

// Fetch post save actions which are used for project creation.
add_action( 'load-post.php', '_translationmanager_handle_actions' );
add_action( 'load-edit.php', '_translationmanager_handle_actions' );

add_action( 'admin_post_tmanager_order_or_update_projects', '_translationmanager_handle_actions' );