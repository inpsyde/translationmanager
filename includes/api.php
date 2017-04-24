<?php

function tm4mlp_api_url( $path = null ) {
	if ( null !== $path ) {
		$path = '/' . $path . '.json';
	}

	return get_option( 'tm4mlp_api_url', 'http://api.eurotext.de/api' ) . $path;
}

/**
 * @param $data
 *
 * @return string|bool ID of the order on API side or false on failure.
 */
function tm4mlp_api_project_create( $data ) {
	global $wp_version;

	$payload = array(
		'meta' => array(
			'system'        => 'WordPress',
			'systemVersion' => $wp_version,
			'plugin'        => TM4MLP_FILE
		),
	);

	$response = wp_remote_request(
		tm4mlp_api_url( 'project' ),
		array(
			'method'  => 'PUT',
			'headers' => array(
				'Content-Type'     => 'application/json',
				'X-Callback'       => get_site_url( null, 'wp-json/tm4mlp/v1/order' ),
				'X-Plugin'         => 'tm4mlp',
				'X-Plugin-Version' => TM4MLP_VERSION,
				'X-System'         => 'WordPress',
				'X-System-Version' => $wp_version,
				'X-Type'           => 'order',
				'apikey'           => get_option( \Tm4mlp\Admin\Options_Page::REFRESH_TOKEN ),
			),
			'body'    => json_encode( array() ),
		)
	);


	$body = wp_remote_retrieve_body( $response );

	$payload = json_decode( $body, true );

	$project_id = (int) $payload['id'];

	// Post each item
	foreach ( $data as $item ) {
		$response = wp_remote_request(
			tm4mlp_api_url( 'project/' . $project_id . '/item' ),
			array(
				'method'  => 'PUT',
				'headers' => array(
					'Content-Type'     => 'application/json',
					'apikey'           => get_option( \Tm4mlp\Admin\Options_Page::REFRESH_TOKEN ),
				),
				'body'    => json_encode( $item ),
			)
		);
	}

	return $project_id;
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
function tm4mlp_api_fetch() {
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
	do_action( TM4MLP_API_PROCESS_ORDER, $data, $target_language, $response );
}