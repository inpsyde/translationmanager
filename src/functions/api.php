<?php

namespace Translationmanager\Functions;

use Translationmanager\Api;
use Translationmanager\Pages\PageOptions;

/**
 * Retrieve API Instance
 *
 * Helper function to retrieve the instance of the translation manager api.
 * It's always return the same instance.
 *
 * @api
 *
 * @since 1.0.0
 *
 * @return \Translationmanager\Api The Instance
 */
function translationmanager_api() {

	static $api = null;

	if ( null === $api ) {
		$api = new Api(
			get_option( PageOptions::REFRESH_TOKEN ),
			'b37270d25d5b3fccf137f7462774fe76',
			get_option( PageOptions::URL, 'http://api.eurotext.de/api/v1' )
		);
	}

	return $api;
}

/**
 * Fetch the latest information about orders from the API.
 *
 * This asks the API about the status of pending orders.
 * Usually this is done twice daily via cron
 * or manually by the site admin.
 *
 * @api
 *
 * @since 1.0.0
 *
 * @return void
 */
function translationmanager_api_fetch() {

	$data            = [];
	$response        = [];
	$target_language = 'no-NE';

	/**
	 * Process incoming translation
	 *
	 * @see   wp_remote_request()
	 *
	 * @todo  C The tag "en-CA" is no ISO, keep using it as it is given in the XLIFF?
	 *
	 * @since 1.0.0
	 *
	 * @param array  $data            Current order data that need to be processed.
	 * @param string $target_language Target language as language tag (like "en-CA").
	 * @param array  $response        The response as of `wp_remote_request()`.
	 */
	do_action( 'translationmanager_api_process_order', $data, $target_language, $response );
}

/**
 * Update Project
 *
 * @api
 *
 * @since 1.0.0
 *
 * @param \WP_Term $project_term The project term to use to retrieve the info to update the post.
 *
 * @return void
 */
function project_update( $project_term ) {

	$project_id = get_term_meta( $project_term->term_id, '_translationmanager_order_id', true );

	if ( ! $project_id ) {
		// ID missing.
		return;
	}

	$translation_data = translationmanager_api()->project()->get( $project_id );

	foreach ( $translation_data['items'] as $items ) {
		foreach ( $items['data'] as $item ) {
			$translation = \Translationmanager\TranslationData::for_incoming( (array) $item );

			/**
			 * Fires for each item or translation received from the API.
			 *
			 * @param \Translationmanager\TranslationData $translation Translation data built from data received from API
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
				 * @param \WP_Post                            $post        Just updated post
				 * @param \Translationmanager\TranslationData $translation Translation data built from data received from API
				 */
				do_action( 'translationmanager_updated_post', $post, $translation );
			}
		}
	}
}
