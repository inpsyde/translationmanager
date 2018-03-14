<?php
\Brain\Monkey\Functions\when( 'wp_remote_retrieve_response_code' )
	->alias( function ( $response ) {

		if ( is_a( $response, 'WP_Error' ) || ! isset( $response['response']['code'] ) ) {
			return '';
		}

		return $response['response']['code'];
	} );
\Brain\Monkey\Functions\when( 'wp_remote_retrieve_body' )
	->alias( function ( $response ) {

		if ( is_a( $response, 'WP_Error' ) || ! isset( $response['body'] ) ) {
			return '';
		}

		return $response['body'];
	} );
