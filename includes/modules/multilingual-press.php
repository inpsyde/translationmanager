<?php

namespace Inpsyde\Tm4mlp;

add_action( 'inpsyde_mlp_loaded', function( \Inpsyde_Property_List_Interface $data ) {

	require_once __DIR__ . '/MlpConnect.php';

	$connect = new MlpConnect( $data->get( 'site_relations' ) );

	// Prepare outgoing data. These will be sent back later unchanged.
	add_filter( 'tm4mlp_sanitize_post', array( $connect, 'prepare_outgoing' ), 10, 3 );
	// where is this called?
	add_filter( 'tm4mlp_get_current_language', array( $connect, 'current_language' ) );
	add_filter( 'tm4mlp_get_languages', array( $connect, 'related_sites' ) );
	// Incoming data
	add_filter( 'tm4mlp_api_translation_update', array( $connect, 'update_translations' ) );
});


