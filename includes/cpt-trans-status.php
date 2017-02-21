<?php

const TM4MLP_TRANS_STATUS = 'tm4mlp_trans_status';

const TM4MLP_TRANS_STATUS_PENDING = 'tm4mlp_pending';

function tm4mlp_cpt_trans_status() {
	register_post_type(
		TM4MLP_TRANS_STATUS,
		array(
			'label'        => __( 'Translations', 'tm4mlp' ),
			'description'  => __( 'Status of your translations', 'tm4mlp' ),
			'public'       => true,
			'capabilities' => array(
				// Removes support for the "Add New" function ( use 'do_not_allow' / false for multisite set ups ).
				'create_posts' => false,
			),
			'map_meta_cap' => true,
			'supports'     => array( '' ),
		)
	);
}

add_action( 'init', 'tm4mlp_cpt_trans_status' );

function tm4mlp_trans_stat_clean() {
	// Remove all WordPress basics as this post type is not meant to be maintained by users.
	remove_meta_box( 'submitdiv', TM4MLP_TRANS_STATUS, 'side' );
	remove_meta_box( 'slugdiv', TM4MLP_TRANS_STATUS, 'normal' );
}

add_action( 'admin_menu', 'tm4mlp_trans_stat_clean' );

// Register Custom Status
function tm4mlp_trans_status_ordered() {
	global $pagenow;

	if ( TM4MLP_TRANS_STATUS != tm4mlp_get_current_post_type() ) {
		// Not in context of t4mlp_trans_status post type.
		return;
	}

	$args = array(
		'label'                     => _x( 'Pending', 'Status General Name', 'tm4mlp' ),
		'label_count'               => _n_noop( 'Pending (%s)', 'Pending (%s)', 'tm4mlp' ),
		'public'                    => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'exclude_from_search'       => true,
	);

	register_post_status( TM4MLP_TRANS_STATUS_PENDING, $args );
}

add_action( 'init', 'tm4mlp_trans_status_ordered', 0 );