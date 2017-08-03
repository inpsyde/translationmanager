<?php
/**
 * If Wonolog (https://github.com/inpsyde/Wonolog) is available, maps plugin log action to Wonolog log action
 *
 * @package translationmanager
 */

if (
	! defined( 'Inpsyde\Wonolog\LOG' )
	|| ! method_exists( 'Inpsyde\Wonolog\Data\Log', 'from_array' )
	|| has_action( TRANSLATIONMANAGER_ACTION_LOG, 'translationmanager_log_action_to_wonolog' )
) {
	return;
}

if ( ! function_exists( 'translationmanager_log_action_to_wonolog' ) ) {

	function translationmanager_log_action_to_wonolog( array $log_data ) {

		do_action( Inpsyde\Wonolog\LOG, Inpsyde\Wonolog\Data\Log::from_array( $log_data ) );
	}
}

add_action( TRANSLATIONMANAGER_ACTION_LOG, function ( array $log_data ) {

	do_action( Inpsyde\Wonolog\LOG, Inpsyde\Wonolog\Data\Log::from_array( $log_data ), 1 );
} );


