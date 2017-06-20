<?php

function tmwp_cpt_cart() {
	register_post_type(
		TMWP_CART,
		array(
			'label'         => __( 'Cart', 'tmwp' ),
			'labels'        => array(
				'name' => __( 'Projects', 'tmwp' ),
				'menu_name' => __( 'Translations', 'tmwp' ),
			),
			'description'   => __( 'What you are about to order.', 'tmwp' ),
			'public'        => true,
			'capabilities'  => array(
				// Removes support for the "Add New" function ( use 'do_not_allow' / false for multisite set ups ).
				'create_posts' => 'do_not_allow',
			),
			'map_meta_cap'  => true,
			'menu_position' => 100,
			'supports'      => array( 'title' ),
			'menu_icon'     => plugins_url( 'public/tm-icon-bw.png', TMWP_FILE ),
			// 'show_in_menu'  => 'edit.php?post_type=' . TMWP_TRANS_STATUS,
		)
	);
}

add_action( 'init', 'tmwp_cpt_cart' );

/**
 * Remove month filter.
 */
function tmwp_cart_remove_month() {
	if ( ! get_current_screen()
	     || TMWP_CART != get_current_screen()->post_type
	) {
		return;
	}

	add_filter( 'months_dropdown_results', '__return_empty_array' );
}

add_action( 'admin_head', 'tmwp_cart_remove_month' );

function tmwp_bulk_actions_cart( $actions ) {
	unset( $actions['edit'] );

	if ( isset( $actions['trash'] ) ) {
		$actions['trash'] = __( 'Remove from project', 'tmwp' );
	}

	return $actions;
}

add_filter( 'bulk_actions-edit-' . TMWP_CART, 'tmwp_bulk_actions_cart' );

/**
 * @param          $actions
 * @param \WP_Post $post
 */
function tmwp_cart_row_actions( $actions, $post ) {
	if ( $post && TMWP_CART != $post->post_type ) {
		return $actions;
	}

	if ( ! isset( $actions['trash'] ) ) {
		return $actions;
	}

	// Delete/Remove only.
	return array(
		'trash' => str_replace(
			'>Trash<',
			'>' . __( 'Remove from project', 'tmwp' ) . '<',
			$actions['trash']
		)
	);
}

add_filter( 'post_row_actions', 'tmwp_cart_row_actions', 10, 2 );

function tmwp_cart_footer( $which ) {
	if ( 'edit-' . TMWP_CART != get_current_screen()->id ) {
		return;
	}

	global $wp_query;
	if ( $wp_query->post_count <= 0 ) {
		return;
	}

	$request = wp_parse_args(
		$_GET,  // Input var ok.
		array(
			TMWP_TAX_PROJECT => null,
		)
	);

	if ( isset( $request['tmwp_project'] ) && $_GET['tmwp_project'] ) {
		$current_slug = $_GET['tmwp_project']; // Input var ok.
		$term         = get_term_by( 'slug', $current_slug, TMWP_TAX_PROJECT );

		if ( ! is_wp_error( $term )
		     && get_term_meta( $term->term_id, '_tmwp_order_id' )
		) {
			// This has an order id so we show the update button.
			require tmwp_get_template( 'admin/cart/manage-cart-extra-tablenav-update.php' );
			_e( 'Thanks for your order.', 'tmwp' );

			return;
		}
	}

	require tmwp_get_template( 'admin/cart/manage-cart-extra-tablenav.php' );
}

add_action( 'manage_posts_extra_tablenav', 'tmwp_cart_footer' );

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
function _tmwp_cart_remove_states( $post_states, $post ) {
	if ( TMWP_CART != $post->post_type ) {
		return $post_states;
	}

	return array();
}

add_filter( 'display_post_states', '_tmwp_cart_remove_states', 10, 2 );

add_action( 'admin_init', array( \Tmwp\Post_Type\Project_Item::class, 'register_post_status' ) );

add_filter(
	'manage_' . TMWP_CART . '_posts_columns',
	array(
		\Tmwp\Post_Type\Project_Item::class,
		'modify_columns'
	)
);

add_filter(
	'manage_edit-' . TMWP_TAX_PROJECT . '_columns',
	array(
		\Tmwp\Taxonomy\Project::class,
		'modify_columns'
	)
);

add_filter(
	TMWP_TAX_PROJECT . '_row_actions',
	array(
		\Tmwp\Taxonomy\Project::class,
		'modify_row_actions'
	),
	10,
	2
);

add_filter( 'views_edit-tmwp_cart', function ( $value ) {
	$request = $_GET; // Input var ok.

	if ( ! isset( $request[ TMWP_TAX_PROJECT ] ) || ! $request[ TMWP_TAX_PROJECT ] ) {
		// Not on a specific project so we can't show details.
		return $value;
	}

	$term = get_term_by( 'slug', $request[ TMWP_TAX_PROJECT ], TMWP_TAX_PROJECT );

	$info = new \Tmwp\Meta_Box\Order_Info( $term->term_id );

	require tmwp_get_template( 'admin/meta-box/project-box.php' );

	require tmwp_get_template( 'admin/cart/manage-cart-title-description.php' );

	return $value;
} );

add_filter( 'bulk_post_updated_messages', function ( $bulk_messages, $bulk_counts ) {

	$bulk_messages[ TMWP_CART ] = array(
		'updated'   => __( 'Project has been updated.', 'tmwp' ),
		'locked'    => ( 1 == $bulk_counts['locked'] ) ? __( '1 page not updated, somebody is editing it.' ) :
			_n( '%s page not updated, somebody is editing it.', '%s pages not updated, somebody is editing them.', $bulk_counts['locked'] ),
		'deleted'   => _n( '%s page permanently deleted.', '%s pages permanently deleted.', $bulk_counts['deleted'] ),
		'trashed'   => _n( '%s page moved to the Trash.', '%s pages moved to the Trash.', $bulk_counts['trashed'] ),
		'untrashed' => _n( '%s page restored from the Trash.', '%s pages restored from the Trash.', $bulk_counts['untrashed'] ),
	);

	if ( isset( $_GET['updated'] ) && - 1 == intval( $_GET['updated'] ) ) { // Input var ok.
		$bulk_messages[ TMWP_CART ]['updated'] = __( 'Project has been created', 'tmwp' );
	}

	return $bulk_messages;
}, 10, 2 );

add_action( 'admin_head-edit.php', function () {
	if ( ! get_current_screen()
	     || TMWP_CART != get_current_screen()->post_type
	) {
		return;
	}

	echo "<style>ul.subsubsub { display: none; }</style>";
} );