<?php

use Translationmanager\Module\Mlp;

add_action( 'inpsyde_mlp_loaded', function ( \Inpsyde_Property_List_Interface $data ) {
	$connect = new Mlp\Connector( $data->get( 'site_relations' ), $data->get( 'content_relations' ) );

	// TM interface hooks to let it know about the environment.
	add_filter( 'translationmanager_get_current_language', array( $connect, 'current_language' ) );
	add_filter( 'translationmanager_get_languages', array( $connect, 'related_sites' ), 10, 2 );

	// Setup the translation workflow.
	add_action( 'translationmanager_outgoing_data', array( $connect, 'prepare_outgoing' ) );
	add_filter( 'translationmanager_post_updater', array( $connect, 'prepare_updater' ) );
} );
