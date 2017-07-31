<?php
/**
 * If Wonolog (https://github.com/inpsyde/Wonolog) is available, maps plugin log action to Wonolog log action
 *
 * @package tmwp
 */

if (
	! defined( 'Inpsyde\Wonolog\LOG' )
	|| ! method_exists( 'Inpsyde\Wonolog\Data\Log', 'from_array' )
	|| has_action( TMWP_ACTION_LOG, 'tmwp_log_action_to_wonolog' )
) {
	return;
}

if ( ! function_exists( 'tmwp_log_action_to_wonolog' ) ) {

	function tmwp_log_action_to_wonolog( array $log_data ) {

		do_action( Inpsyde\Wonolog\LOG, Inpsyde\Wonolog\Data\Log::from_array( $log_data ) );
	}
}

add_action( TMWP_ACTION_LOG, function ( array $log_data ) {

	do_action( Inpsyde\Wonolog\LOG, Inpsyde\Wonolog\Data\Log::from_array( $log_data ), 1 );
} );


