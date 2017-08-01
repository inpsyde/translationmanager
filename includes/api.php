<?php

function translationmanager_api_url( $path = null ) {
	return translationmanager_api()->get_url( $path );
}

/**
 * Instance of the TRANSLATIONMANAGER API.
 *
 * @return \Translationmanager\Api
 */
function translationmanager_api() {
	static $api;

	if ( null === $api ) {
		$api = new \Translationmanager\Api(
			get_option( \Translationmanager\Admin\Options_Page::REFRESH_TOKEN ),
			'b37270d25d5b3fccf137f7462774fe76',
			get_option( \Translationmanager\Admin\Options_Page::URL, 'http://api.eurotext.de/api/v1' )
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
 * @since 1.0.0
 */
function translationmanager_api_fetch() {
	$data            = array();
	$response        = array(); // wp_remote_request()
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
	do_action( TRANSLATIONMANAGER_API_PROCESS_ORDER, $data, $target_language, $response );
}