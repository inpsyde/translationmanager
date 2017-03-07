<?php

function tm4mlp_cpt_cart() {
	register_post_type(
		TM4MLP_CART,
		array(
			'label'         => __( 'Cart', 'tm4mlp' ),
			'labels'        => array(
				'name' => __( 'Overview', 'tm4mlp' ),
			),
			'description'   => __( 'What you are about to order.', 'tm4mlp' ),
			'public'        => true,
			'capabilities'  => array(
				// Removes support for the "Add New" function ( use 'do_not_allow' / false for multisite set ups ).
				'create_posts' => 'do_not_allow',
			),
			'map_meta_cap'  => true,
			'menu_position' => 100,
			'supports'      => array( 'title' ),
			// 'show_in_menu'  => 'edit.php?post_type=' . TM4MLP_TRANS_STATUS,
		)
	);
}

add_action( 'init', 'tm4mlp_cpt_cart' );

/**
 * Remove month filter.
 */
function tm4mlp_cart_remove_month() {
	if ( ! get_current_screen()
	     || TM4MLP_CART != get_current_screen()->post_type
	) {
		return;
	}

	add_filter( 'months_dropdown_results', '__return_empty_array' );
}

add_action( 'admin_head', 'tm4mlp_cart_remove_month' );

function tm4mlp_bulk_actions_cart( $actions ) {
	unset( $actions['edit'] );

	if ( isset( $actions['trash'] ) ) {
		$actions['trash'] = __( 'Remove from project', 'tm4mlp' );
	}

	return $actions;
}

add_filter( 'bulk_actions-edit-' . TM4MLP_CART, 'tm4mlp_bulk_actions_cart' );

/**
 * @param          $actions
 * @param \WP_Post $post
 */
function tm4mlp_cart_row_actions( $actions, $post ) {
	if ( $post && TM4MLP_CART != $post->post_type ) {
		return $actions;
	}

	if ( ! isset( $actions['trash'] ) ) {
		return $actions;
	}

	// Delete/Remove only.
	return array(
		'trash' => str_replace(
			'>Trash<',
			'>' . __( 'Remove from project', 'tm4mlp' ) . '<',
			$actions['trash']
		)
	);
}

add_filter( 'post_row_actions', 'tm4mlp_cart_row_actions', 10, 2 );

function tm4mlp_cart_footer( $which ) {
	if ( 'edit-' . TM4MLP_CART != get_current_screen()->id ) {
		return;
	}

	global $wp_query;
	if ( $wp_query->post_count <= 0 ) {
		return;
	}

	if ( isset( $_GET['tm4mlp_project'] ) && $_GET['tm4mlp_project'] ) { // Input var ok.
		$current_slug = $_GET['tm4mlp_project']; // Input var ok.
		$term         = get_term_by( 'slug', $current_slug, TM4MLP_TAX_PROJECT );

		if ( ! is_wp_error( $term )
		     && get_term_meta( $term->term_id, '_tm4mlp_order_id' )
		) {
			// This has an order id so we don't show the order button.
			return;
		}
	}

	require tm4mlp_get_template( 'admin/cart/manage-cart-extra-tablenav.php' );
}

add_action( 'manage_posts_extra_tablenav', 'tm4mlp_cart_footer' );

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
function _tm4mlp_cart_remove_states( $post_states, $post ) {
	if ( TM4MLP_CART != $post->post_type ) {
		return $post_states;
	}

	return array();
}

add_filter( 'display_post_states', '_tm4mlp_cart_remove_states', 10, 2 );

add_action( 'admin_init', array( \Tm4mlp\Post_Type\Project_Item::class, 'register_post_status' ) );

// Show project name in trash.
add_filter(
	'manage_' . TM4MLP_CART . '_posts_columns',
	array(
		\Tm4mlp\Post_Type\Project_Item::class,
		'modify_columns'
	)
);

add_filter( 'views_edit-tm4mlp_cart', function ( $value ) {
	$request = $_GET; // Input var ok.

	if ( ! isset( $request[ TM4MLP_TAX_PROJECT ] ) || ! $request[ TM4MLP_TAX_PROJECT ] ) {
		// Not on a specific project so we can't show details.
		return $value;
	}

	$info = new \Tm4mlp\Meta_Box\Order_Info();

	require tm4mlp_get_template( 'admin/meta-box/project-box.php' );

	return $value;
} );

add_filter( 'bulk_post_updated_messages', function ( $bulk_messages, $bulk_counts ) {

	$bulk_messages[ TM4MLP_CART ] = array(
		'updated'   => __( 'Project has been updated.', 'tm4mlp' ),
		'locked'    => ( 1 == $bulk_counts['locked'] ) ? __( '1 page not updated, somebody is editing it.' ) :
			_n( '%s page not updated, somebody is editing it.', '%s pages not updated, somebody is editing them.', $bulk_counts['locked'] ),
		'deleted'   => _n( '%s page permanently deleted.', '%s pages permanently deleted.', $bulk_counts['deleted'] ),
		'trashed'   => _n( '%s page moved to the Trash.', '%s pages moved to the Trash.', $bulk_counts['trashed'] ),
		'untrashed' => _n( '%s page restored from the Trash.', '%s pages restored from the Trash.', $bulk_counts['untrashed'] ),
	);

	if ( isset( $_GET['updated'] ) && - 1 == intval( $_GET['updated'] ) ) { // Input var ok.
		$bulk_messages[ TM4MLP_CART ]['updated'] = __( 'Project has been created', 'tm4mlp' );
	}

	return $bulk_messages;
}, 10, 2 );