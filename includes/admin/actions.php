<?php

/**
 * @param $arguments
 *
 * @return bool|int
 */
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
		TMWP_FILTER_BEFORE_ADD_TO_PROJECT,
		true,
		$project,
		$request['post_ID'],
		$request['tmwp_language']
	);

	if (true !== $valid) {
		return false;
	}

	// Remember the last manipulated project.
	update_user_meta( get_current_user_id(), 'tmwp_project_recent', $project );

	// Iterate translations
	foreach ( $request['tmwp_language'] as $lang_id ) {
		$handler->add_translation( $project, (int) $request['post_ID'], $lang_id );
	}

	/**
	 * Filter the output of the `tmwp_action_project_add_translation` function.
	 *
	 * After adding posts to a project / cart it will redirect to this project.
	 * One last time you can filter to which project it will redirect (by using the ID)
	 * or if should'nt redirect at all (by setting the value to "false").
	 *
	 * @param int   $project ID of the project (actually a term ID).
	 * @param int   $post_id ID of the post that will be added to the cart.
	 * @param int[] $languages IDs of the target languages (assoc pair).
	 *
	 * @see tmwp_action_project_add_translation() where this filter resides.
	 * @see tmwp_get_languages() how languages are gathered.
	 */
	return apply_filters(
		TMWP_FILTER_PROJECT_ADD_TRANSLATION,
		$project,
		$request['post_ID'],
		$request['tmwp_language']
	);
}

function _tmwp_handle_actions() {

	$post_data = filter_input_array(
		INPUT_POST,
		array(
			TMWP_ACTION_PROJECT_ORDER           => FILTER_SANITIZE_STRING,
			TMWP_ACTION_PROJECT_UPDATE          => FILTER_SANITIZE_STRING,
			TMWP_ACTION_PROJECT_ADD_TRANSLATION => FILTER_SANITIZE_STRING,
			'_tmwp_project_id'                  => FILTER_SANITIZE_STRING,
			'tmwp_project_id'                   => FILTER_SANITIZE_NUMBER_INT,
			'tmwp_language'                     => array(
				'filter' => FILTER_SANITIZE_STRING,
				'flags'  => FILTER_FORCE_ARRAY
			)
		)
	);

	// If nothing submitted or no action detected we stop processing.
	if (
		! $post_data
		|| (
			null === $post_data[ TMWP_ACTION_PROJECT_ORDER ]
			&& null === $post_data[ TMWP_ACTION_PROJECT_UPDATE ]
			&& null === $post_data[ TMWP_ACTION_PROJECT_ADD_TRANSLATION ]
		)
	) {
		return;
	}

	if ( null !== $post_data[ TMWP_ACTION_PROJECT_ORDER ] ) {
		$term = get_term_by( 'slug', $post_data['_tmwp_project_id'], TMWP_TAX_PROJECT );

		_tmwp_project_order( $term );

		wp_redirect(
			get_admin_url(
				null,
				'edit.php?' .
				http_build_query(
					array(
						TMWP_TAX_PROJECT => $post_data[ '_tmwp_project_id' ],
						'post_type'      => TMWP_CART,
					)
				)
			)
		);

		wp_die( '', '', array( 'response' => 302 ) );
	}

	if ( null !== $post_data[ TMWP_ACTION_PROJECT_UPDATE ] ) {
		$term = get_term_by( 'slug', $post_data['_tmwp_project_id'], TMWP_TAX_PROJECT );

		_tmwp_project_update( $term );

		wp_redirect(
			get_admin_url(
				null,
				'edit.php?' .
				http_build_query(
					array(
						TMWP_TAX_PROJECT => $post_data[ '_tmwp_project_id' ],
						'post_type'      => TMWP_CART,
					)
				)
			)
		);

		wp_die( '', '', array( 'response' => 302 ) );
	}

	if ( null !== $post_data[ TMWP_ACTION_PROJECT_ADD_TRANSLATION ] ) {

		$updater = new \Tmwp\Admin\Cart_Updater();
		$updater->setup();

		$project = tmwp_action_project_add_translation(
			array(
				'tmwp_language'   => $post_data['tmwp_language'],
				'tmwp_project_id' => $post_data['tmwp_project_id'],
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
						TMWP_TAX_PROJECT => get_term_field( 'slug', $project ),
						'post_type'      => TMWP_CART,
						'updated'        => - 1,
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

		$source_post = get_post( $post->_tmwp_post_id );
		if ( ! $source_post ) {
			continue;
		}

		$source_site_id = get_current_blog_id();

		$data = \Tmwp\Translation_Data::for_outgoing(
			$source_post,
			$source_site_id,
			$post->_tmwp_target_id,
			$languages[ $post->_tmwp_target_id ]->get_lang_code()
		);

		/**
		 * Fires before translation data is transfered to the API.
		 *
		 * Data can be edited in place by listeners.
		 *
		 * @param \Tmwp\Translation_Data $data
		 */
		do_action_ref_array( TMWP_OUTGOING_DATA, array( $data ) );

		tmwp_api()->project_item()->create( $project_id, $data->to_array() );
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

	$translation_data = tmwp_api()->project()->get( $project_id );

	foreach ( $translation_data['items'] as $item ) {

		$translation = \Tmwp\Translation_Data::for_incoming( (array) $item['data'] );

		/**
		 * Fires for each item or translation received from the API.
		 *
		 * @param \Tmwp\Translation_Data $translation Translation data built from data received from API
		 */
		do_action( TMWP_INCOMING_DATA, $translation );

		/**
		 * Filters the updater that executed have to return the updated post
		 */
		$updater = apply_filters( TMWP_POST_UPDATER, null, $translation );

		$post = is_callable( $updater ) ? $updater( $translation ) : null;

		if ( $post instanceof \WP_Post ) {

			/**
			 * Fires after the updater has updated the post.
			 *
			 * @param \WP_Post $post                      Just updated post
			 * @param \Tmwp\Translation_Data $translation Translation data built from data received from API
			 */
			do_action( TMWP_UPDATED_POST, $post, $translation );
		}

	}
}

// Fetch post save actions which are used for project creation.
add_action( 'load-post.php', '_tmwp_handle_actions' );
add_action( 'load-edit.php', '_tmwp_handle_actions' );

add_action( 'admin_post_tmwp_order_or_update_projects', '_tmwp_handle_actions' );