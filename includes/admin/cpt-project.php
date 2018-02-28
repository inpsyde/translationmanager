<?php

function translationmanager_cpt_cart() {

	register_post_type(
		'tm_cart',
		array(
			'label'         => esc_html__( 'Cart', 'translationmanager' ),
			'labels'        => array(
				'name'      => esc_html__( 'Projects', 'translationmanager' ),
				'menu_name' => esc_html__( 'Translations', 'translationmanager' ),
			),
			'description'   => esc_html__( 'What you are about to order.', 'translationmanager' ),
			'public'        => true,
			'capabilities'  => array(
				// Removes support for the "Add New" function ( use 'do_not_allow' / false for multisite set ups ).
				'create_posts' => 'do_not_allow',
			),
			'map_meta_cap'  => true,
			'menu_position' => 100,
			'supports'      => array( 'title' ),
			'menu_icon'     => plugins_url( 'public/tm-icon-bw.png', TRANSLATIONMANAGER_FILE ),
			// 'show_in_menu'  => 'edit.php?post_type=translationmanager_trans_status',
		)
	);

}

add_action( 'init', 'translationmanager_cpt_cart' );

/**
 * Remove month filter.
 */
function tmanager_cart_remove_month() {

	if ( ! get_current_screen()
	     || 'tm_cart' !== get_current_screen()->post_type
	) {
		return;
	}

	add_filter( 'months_dropdown_results', '__return_empty_array' );
}

add_action( 'admin_head', 'tmanager_cart_remove_month' );

function translationmanager_bulk_actions_cart( $actions ) {

	unset( $actions['edit'] );

	if ( isset( $actions['trash'] ) ) {
		$actions['trash'] = esc_html__( 'Remove from project', 'translationmanager' );
	}

	return $actions;
}

add_filter( 'bulk_actions-edit-tm_cart', 'translationmanager_bulk_actions_cart' );

/**
 * @param          $actions
 * @param \WP_Post $post
 */
function tmanager_cart_row_actions( $actions, $post ) {

	if ( $post && 'tm_cart' !== $post->post_type ) {
		return $actions;
	}

	if ( ! isset( $actions['trash'] ) ) {
		return $actions;
	}

	// Delete/Remove only.
	return array(
		'trash' => str_replace(
			'>Trash<',
			'>' . esc_html__( 'Remove from project', 'translationmanager' ) . '<',
			$actions['trash']
		),
	);
}

add_filter( 'post_row_actions', 'tmanager_cart_row_actions', 10, 2 );

//function tmanager_cart_footer( $which ) {
//	if ( 'edit-tmanager_cart' !== get_current_screen()->id ) {
//		return;
//	}
//
//	global $wp_query;
//	if ( $wp_query->post_count <= 0 ) {
//		return;
//	}
//
//	$request = wp_parse_args(
//		$_GET,  // Input var ok.
//		array(
//			'translationmanager_project' => null,
//		)
//	);
//
//	if ( isset( $request['translationmanager_project'] ) && $_GET['translationmanager_project'] ) {
//		$current_slug = $_GET['translationmanager_project']; // Input var ok.
//		$term         = get_term_by( 'slug', $current_slug, 'translationmanager_project' );
//
//		if ( ! is_wp_error( $term )
//		     && get_term_meta( $term->term_id, '_translationmanager_order_id' )
//		) {
//			// This has an order id so we show the update button.
//			require translationmanager_get_template( 'admin/cart/manage-cart-extra-tablenav-update.php' );
//			_e( 'Thanks for your order.', 'translationmanager' );
//
//			return;
//		}
//	}
//
//	require translationmanager_get_template( 'admin/cart/manage-cart-extra-tablenav.php' );
//}
//
//add_action( 'manage_posts_extra_tablenav', 'tmanager_cart_footer' );

/**
 * Hide WP status.
 *
 * @deprecated 1.0.0 State shall be shown but different.
 *
 * @param $post_states
 * @param $post
 *
 * @return array
 */
function _tmanager_cart_remove_states( $post_states, $post ) {

	if ( 'tm_cart' !== $post->post_type ) {
		return $post_states;
	}

	return array();
}

add_filter( 'display_post_states', '_tmanager_cart_remove_states', 10, 2 );

add_action( 'admin_init', array( \Translationmanager\Post_Type\Project_Item::class, 'register_post_status' ) );

add_filter(
	'manage_tm_cart_posts_columns',
	array(
		\Translationmanager\Post_Type\Project_Item::class,
		'modify_columns',
	)
);

add_filter(
	'manage_edit-translationmanager_project_columns',
	array(
		\Translationmanager\Taxonomy\Project::class,
		'modify_columns',
	)
);

add_filter(
	'translationmanager_project_row_actions',
	array(
		\Translationmanager\Taxonomy\Project::class,
		'modify_row_actions',
	),
	10,
	2
);

add_filter( 'views_edit-tm_cart', function ( $value ) {

	$slug = sanitize_title( filter_input( INPUT_GET, 'translationmanager_project', FILTER_SANITIZE_STRING ) );

	if ( ! $slug ) {
		// Not on a specific project so we can't show details.
		return $value;
	}

	$term = get_term_by( 'slug', $slug, 'translationmanager_project' );
	// This is used inside the view
	$info = new \Translationmanager\Meta_Box\Order_Info( $term->term_id );

	require translationmanager_get_template( 'admin/meta-box/project-box.php' );
	require translationmanager_get_template( 'admin/cart/manage-cart-title-description.php' );

	return $value;
} );

add_filter( 'bulk_post_updated_messages', function ( $bulk_messages, $bulk_counts ) {

	$bulk_messages['tm_cart'] = array(
		'updated'   => esc_html__( 'Project has been updated.', 'translationmanager' ),
		'locked'    => ( 1 === $bulk_counts['locked'] ) ? esc_html__( '1 page not updated, somebody is editing it.', 'translationmanager' ) :
			_n( '%s page not updated, somebody is editing it.', '%s pages not updated, somebody is editing them.', $bulk_counts['locked'] ),
		'deleted'   => _n( '%s page permanently deleted.', '%s pages permanently deleted.', $bulk_counts['deleted'] ),
		'trashed'   => _n( '%s page moved to the Trash.', '%s pages moved to the Trash.', $bulk_counts['trashed'] ),
		'untrashed' => _n( '%s page restored from the Trash.', '%s pages restored from the Trash.', $bulk_counts['untrashed'] ),
	);

	$updated = filter_input( INPUT_GET, 'updated', FILTER_SANITIZE_NUMBER_INT );
	if ( - 1 === $updated ) {
		$bulk_messages['tm_cart']['updated'] = esc_html__( 'Project has been created', 'translationmanager' );
	}

	return $bulk_messages;
}, 10, 2 );

add_action( 'admin_head-edit.php', function () {

	if ( ! get_current_screen() || 'tm_cart' !== get_current_screen()->post_type ) {
		return;
	}

	echo "<style>ul.subsubsub { display: none; }</style>";
} );