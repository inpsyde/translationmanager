<?php

function tm4mlp_api_url( $path = null ) {
	if ( null !== $path ) {
		$path = '/' . $path . '.json';
	}

	return get_option( 'tm4mlp_api_url', 'http://inpsyde.local:8000/api' . $path );
}

/**
 * @param $data
 *
 * @return string|bool ID of the order on API side or false on failure.
 */
function tm4mlp_api_order( $data ) {
	$response = wp_remote_request(
		tm4mlp_api_url( 'order' ),
		array(
			'method' => 'PUT',
			'header' => array(
				'Content-Type' => 'application/json',
			),
			'body'   => json_encode( $data ),
		)
	);

	$body = wp_remote_retrieve_body( $response );

	$payload = json_decode( $body, true );

	if ( ! isset( $payload['data'] )
	     || ! isset( $payload['data']['order_id'] )
	     || ! $payload['data']['order_id']
	) {
		return false;
	}

	return $payload['data']['order_id'];
}