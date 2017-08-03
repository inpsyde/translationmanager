<?php
/**
 * Simple forward logging to debug.log
 *
 * @package translationmanager
 */

add_action( TRANSLATIONMANAGER_ACTION_LOG, function ( array $log_data ) {

	if ( ! array_key_exists( 'message', $log_data ) || ! $log_data['message'] ) {
		return;
	}

	$context = '';
	if ( array_key_exists( 'context', $log_data ) && $log_data['context'] ) {
		$context = '; context: ' . json_encode( $log_data['context'] );
	}

	error_log( TRANSLATIONMANAGER_PREFIX . ': ' . esc_html( $log_data['message'] ) . $context );
} );


