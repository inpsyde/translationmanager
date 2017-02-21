<?php

const TM4MLP_TRANSLATION_STATUS_POST_TYPE = 'tm4mlp_trans_status';

function tm4mlp_cpt_trans_status() {
	register_post_type(
		TM4MLP_TRANSLATION_STATUS_POST_TYPE,
		array(
			'label' => __( 'Translations', 'tm4mlp' ),
			'description' => __('Status of your translations', 'tm4mlp'),
			'public' => true,
			'capabilities' => array(
				// Removes support for the "Add New" function ( use 'do_not_allow' / false for multisite set ups ).
				'create_posts' => false,
			),
			'map_meta_cap' => true,
			'supports' => array( '' ),
		)
	);
}

add_action( 'init', 'tm4mlp_cpt_trans_status' );

function tm4mlp_trans_stat_clean() {
	// Remove all WordPress basics as this post type is not meant to be maintained by users.
	remove_meta_box( 'submitdiv', TM4MLP_TRANSLATION_STATUS_POST_TYPE, 'side' );
	remove_meta_box( 'slugdiv', TM4MLP_TRANSLATION_STATUS_POST_TYPE, 'normal' );
}

add_action( 'admin_menu', 'tm4mlp_trans_stat_clean' );