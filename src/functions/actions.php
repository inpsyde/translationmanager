<?php
/**
 * Actions Group Functions
 *
 * @package Translationmanager\Functions
 */

namespace Translationmanager\Functions;

use Translationmanager\Plugin;

/**
 * Add Project Translation
 *
 * @since 1.0.0
 *
 * @throws \Exception In case the project cannot be created
 *
 * @param array $arguments Arguments to add languages.
 *
 * @return bool|int
 */
function action_project_add_translation( $arguments ) {

	$request = wp_parse_args( $arguments, [
		'translationmanager_language'   => array_keys( get_languages() ),
		'translationmanager_project_id' => null,
	] );

	$handler = new \Translationmanager\ProjectHandler();

	$project = (int) $request['translationmanager_project_id'];

	if ( ! $project ) {
		$project = $handler->create_project(
			sprintf( esc_html__( 'Project %s', 'translationmanager' ), date( 'Y-m-d H:i:s' ) )
		);
	}

	/**
	 * Runs before adding translations to the project.
	 *
	 * You might add other things to the project before the translations kick in
	 * or check against some other things (like account balance) to stop adding things to the project
	 * and show some error message.
	 *
	 * For those scenarios this filter allows turn it's value into false.
	 * In that case it will neither add things to the project/project
	 * nor redirect to the project- / project-view.
	 *
	 * @param bool  $valid     Initially true and can be torn to false to stop adding items to the project.
	 * @param int   $project   ID of the project (actually a term ID).
	 * @param int   $post_id   ID of the post that will be added to the project.
	 * @param int[] $languages IDs of the target languages (assoc pair).
	 *
	 * @see wp_insert_post() actions and filter to access each single transation that is added to project.
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

	// Iterate translations.
	foreach ( $request['translationmanager_language'] as $lang_id ) {
		$handler->add_translation( $project, (int) $request['post_ID'], $lang_id );
	}

	/**
	 * Filter the output of the `translationmanager_action_project_add_translation` function.
	 *
	 * After adding posts to a project / project it will redirect to this project.
	 * One last time you can filter to which project it will redirect (by using the ID)
	 * or if should'nt redirect at all (by setting the value to "false").
	 *
	 * @param int   $project   ID of the project (actually a term ID).
	 * @param int   $post_id   ID of the post that will be added to the project.
	 * @param int[] $languages IDs of the target languages (assoc pair).
	 *
	 * @see \Translationmanager\Functions\action_project_add_translation() where this filter resides.
	 * @see \Translationmanager\Functions\get_languages() how languages are gathered.
	 */
	return apply_filters(
		'translationmanager_action_project_add_translation',
		$project,
		$request['post_ID'],
		$request['translationmanager_language']
	);
}

/**
 * Handle Actions for Translations
 *
 * @since 1.0.0
 *
 * @return void
 */
function handle_actions() {

	$post_data = filter_input_array( INPUT_POST, [
		'translationmanager_action_project_order'           => FILTER_SANITIZE_STRING,
		'translationmanager_action_project_update'          => FILTER_SANITIZE_STRING,
		'translationmanager_action_project_add_translation' => FILTER_SANITIZE_STRING,
		'_translationmanager_project_id'                    => FILTER_SANITIZE_STRING,
		'translationmanager_project_id'                     => FILTER_SANITIZE_NUMBER_INT,
		'post_ID'                                           => FILTER_SANITIZE_NUMBER_INT,
		'translationmanager_language'                       => [
			'filter' => FILTER_SANITIZE_STRING,
			'flags'  => FILTER_FORCE_ARRAY,
		],
	] );

	if ( ! $post_data ) {
		return;
	}

	// If nothing submitted or no action detected we stop processing.
	if (
		null === $post_data['translationmanager_action_project_order']
		&& null === $post_data['translationmanager_action_project_update']
		&& null === $post_data['translationmanager_action_project_add_translation']
	) {
		return;
	}

	$actions = [
		$post_data['translationmanager_action_project_order'],
		$post_data['translationmanager_action_project_update'],
	];

	foreach ( $actions as $action ) {
		if ( null !== $action ) {
			$term = get_term_by( 'slug', $post_data['_translationmanager_project_id'], 'translationmanager_project' );

			update_project_order_meta( $term );

			redirect_admin_page_network( 'edit.php?', [
				'translationmanager_project' => $post_data['_translationmanager_project_id'],
				'post_type'                  => 'project_item',
			] );
		}
	}

	if ( null !== $post_data['translationmanager_action_project_add_translation'] ) {
		$updater = new \Translationmanager\Admin\ProjectUpdater();
		$updater->init();

		$project = action_project_add_translation( [
			'translationmanager_language'   => $post_data['translationmanager_language'],
			'translationmanager_project_id' => $post_data['translationmanager_project_id'],
			'post_ID'                       => $post_data['post_ID'],
		] );

		if ( false === $project ) {
			// Project has been invalidated so we don't redirect there.
			return;
		}

		redirect_admin_page_network( 'edit.php?', [
			'translationmanager_project' => get_term_field( 'slug', $project ),
			'post_type'                  => 'project_item',
			'updated'                    => - 1,
		] );
	}
}

/**
 * Project Order
 *
 * @todo  Move in cpt-order.
 *
 * @since 1.0.0
 *
 * @param \WP_Term $project_term The project term associated.
 *
 * @return mixed Whatever the update_term_meta returns
 */
function update_project_order_meta( \WP_Term $project_term ) {

	global $wp_version;

	$project_id = translationmanager_api()->project()->create(
		new \Translationmanager\Domain\Project(
			'WordPress',
			$wp_version,
			'translationmanager',
			Plugin::VERSION,
			$project_term->name
		)
	);

	if ( ! $project_id ) {
		return false;
	}

	// Posts get collected by post type.
	$post_types    = [];
	$languages     = get_languages();
	$project_items = get_project_items( $project_term->term_id );

	foreach ( $project_items as $post ) {
		if ( ! $post->_translationmanager_post_id || ! isset( $languages[ $post->_translationmanager_target_id ] ) ) {
			// Invalid state, try next one.
			continue;
		}

		$source_post = get_post( $post->_translationmanager_post_id );
		if ( ! $source_post ) {
			continue;
		}

		$source_site_id = get_current_blog_id();

		$data = \Translationmanager\TranslationData::for_outgoing(
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
		 * @param \Translationmanager\TranslationData $data
		 */
		do_action_ref_array( 'translationmanager_outgoing_data', [ $data ] );

		$post_types[ $languages[ $post->_translationmanager_target_id ]->get_lang_code() ][ $source_post->post_type ][] = $data->to_array();
	}

	foreach ( $post_types as $post_type_target_language => $post_types_data ) {
		foreach ( $post_types_data as $post_type_name => $post_type_content ) {
			translationmanager_api()
				->project_item()
				->create( $project_id, $post_type_name, $post_type_target_language, $post_type_content );
		}
	}

	return update_term_meta( $project_term->term_id, '_translationmanager_order_id', $project_id );
}
