<?php
/**
 * Actions Group Functions
 *
 * @package Translationmanager\Functions
 */

namespace Translationmanager\Functions;

use Translationmanager\ProjectHandler;

/**
 * Add Project Translation
 *
 * @since 1.0.0
 *
 * @throws \Exception In case the project cannot be created.
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
	$project = $request['translationmanager_project_id'];
	$handler = new \Translationmanager\ProjectHandler();

	if ( '-1' === $project ) {
		ProjectHandler::create_project_using_date();
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
