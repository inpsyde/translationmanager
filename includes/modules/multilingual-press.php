<?php

use Tmwp\Module\Mlp_Connect;

add_action( 'inpsyde_mlp_loaded', function ( \Inpsyde_Property_List_Interface $data ) {
	$connect = new Mlp_Connect( $data->get( 'site_relations' ) );

	// TM interface hooks to let it know about the environment.
	add_filter( 'tmwp_get_current_language', array( $connect, 'current_language' ) );
	add_filter( 'tmwp_get_languages', array( $connect, 'related_sites' ), 10, 2 );

	// Prepare outgoing data. These will be sent back later unchanged.
	add_filter( TMWP_SANITIZE_POST, array( $connect, 'prepare_outgoing' ), 10, 3 );

	// Incoming data.
	add_filter( 'tmwp_api_translation_update', array( $connect, 'update_translations' ) );
} );
