<?php

use Tmwp\Module\Mlp_Connect;

add_action( 'inpsyde_mlp_loaded', function ( \Inpsyde_Property_List_Interface $data ) {
	$connect = new Mlp_Connect( $data->get( 'site_relations' ), $data->get( 'content_relations' ) );

	// TM interface hooks to let it know about the environment.
	add_filter( 'tmwp_get_current_language', array( $connect, 'current_language' ) );
	add_filter( 'tmwp_get_languages', array( $connect, 'related_sites' ), 10, 2 );

	// Setup the translation workflow
	add_filter( TMWP_OUTGOING_DATA, array( $connect, 'prepare_outgoing' ) );
	add_action( TMWP_POST_UPDATER, array( $connect, 'prepare_updater' ) );
	add_action( TMWP_UPDATED_POST, array( $connect, 'notify_third_party' ), 10, 2 );
} );
