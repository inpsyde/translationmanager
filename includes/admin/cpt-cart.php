<?php

function tm4mlp_cpt_cart() {
	register_post_type(
		TM4MLP_CART,
		array(
			'label'        => __( 'Cart', 'tm4mlp' ),
			'description'  => __( 'What you are about to order.', 'tm4mlp' ),
			'public'       => true,
			'capabilities' => array(
				// Removes support for the "Add New" function ( use 'do_not_allow' / false for multisite set ups ).
				'create_posts' => false,
			),
			'map_meta_cap' => true,
			'supports'     => array( 'title' ),
			'show_in_menu' => 'edit.php?post_type=' . TM4MLP_TRANS_STATUS,
		)
	);
}

add_action( 'init', 'tm4mlp_cpt_cart' );

function tm4mlp_cart_clean() {
	// Remove all WordPress basics as this post type is not meant to be maintained by users.
	remove_meta_box( 'submitdiv', TM4MLP_CART, 'side' );
	remove_meta_box( 'slugdiv', TM4MLP_CART, 'normal' );

	$screen = get_current_screen();

	if ( TM4MLP_CART != $screen->post_type ) {
		return;
	}

	add_filter( 'months_dropdown_results', '__return_empty_array' );
}

add_action( 'admin_head', 'tm4mlp_cart_clean' );

function tm4mlp_bulk_actions_cart( $actions ) {
	unset( $actions['edit'] );

	if ( isset( $actions['trash'] ) ) {
		$actions['trash'] = __( 'Remove from cart', 'tm4mlp' );
	}

	return $actions;
}

add_filter( 'bulk_actions-edit-' . TM4MLP_CART, 'tm4mlp_bulk_actions_cart' );

/**
 * Cart items have no trash.
 *
 * @param $post_id
 */
function tm4mlp_cart_trashed( $post_id ) {
	$post = get_post( $post_id );

	if ( TM4MLP_CART != $post->post_type ) {
		return;
	}

	wp_delete_post( $post_id );
}

add_action( 'trashed_post', 'tm4mlp_cart_trashed' );

/**
 * @param          $actions
 * @param \WP_Post $post
 */
function tm4mlp_cart_row_actions( $actions, $post ) {
	if ( $post && TM4MLP_CART != $post->post_type ) {
	return $actions;

	}

	// TODO: Delete/Remove only.

	return array();
}

add_filter( 'post_row_actions', 'tm4mlp_cart_row_actions', 10, 2 );

function tm4mlp_cart_footer( $which ) {
	if ( 'bottom' != $which ) {
//		return;
	}

	if ( 'edit-' . TM4MLP_CART != get_current_screen()->id ) {
		return;
	}

	require tm4mlp_get_template( 'admin/cart/manage-cart-extra-tablenav.php' );
}

add_action( 'manage_posts_extra_tablenav', 'tm4mlp_cart_footer' );

add_action( 'load-edit.php', 'tm4mlp_order_translation' );

function tm4mlp_order_translation() {
	if ( ! get_current_screen() || TM4MLP_CART != get_current_screen()->post_type ) {
		// Not our context so we ignore it.
		return;
	}

	$request = $_GET; // Input var ok.

	if ( ! isset( $request['tm4mlp_order_translation'] ) ) {
		// Cart table but order button not clicked so we ignore it.
		return;
	}

	// List of post IDs / cart items.
	$cart_items = array();

	if ( isset( $request['post'] ) && $request['post'] ) {
		$cart_items = array_map( 'intval', $request['post'] );
	}

	if ( ! $cart_items ) {
		$cart_items = get_posts(
			array(
				'posts_per_page' => - 1,
				'post_type'      => TM4MLP_CART,
			)
		);
	}

	// Create order post
	$order_id = wp_insert_post(
		array(
			'post_title' => sprintf(
				__( '%d items on %s', 'tm4mlp' ),
				count( $cart_items ),
				date( 'Y-m-d' )
			),
			'post_type'  => TM4MLP_ORDER,
		)
	);

	if ( is_wp_error( $order_id ) ) {
		tm4mlp_die();
	}

	// Gather order data
	// and add entities to new parent.
	$order_data = array();
	foreach ( $cart_items as $cart_item ) {
//		$order_data[] = apply_filters( TM4MLP_SANITIZE_POST, $cart_item );

		wp_update_post(
			array(
				'ID'          => $cart_item,
				'post_parent' => $order_id,
				'post_type'   => 'tm4mlp_order',
			)
		);
	}

//	do_action( TM4MLP_API_PROCESS_ORDER, $order_data );

	wp_redirect(
		get_admin_url( null, 'post.php?action=edit&post=' . $order_id )
	);
}

add_action(
	'admin_head',
	function () {
		if ( isset( $_GET['success'] ) ) {
			echo '<div class="notice notice-success is-dismissible" ><p >'
			     . esc_html__( $_GET['success'] )
			     . '</p></div>';
		}
	}
);