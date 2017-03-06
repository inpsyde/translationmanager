<?php

function tm4mlp_cpt_cart() {
	register_post_type(
		TM4MLP_CART,
		array(
			'label'         => __( 'Cart', 'tm4mlp' ),
			'labels'        => array(
				'name' => __( 'Create cart', 'tm4mlp' ),
			),
			'description'   => __( 'What you are about to order.', 'tm4mlp' ),
			'public'        => true,
			'capabilities'  => array(
				// Removes support for the "Add New" function ( use 'do_not_allow' / false for multisite set ups ).
				'create_posts' => false,
			),
			'map_meta_cap'  => true,
			'menu_position' => 100,
			'supports'      => array( 'title' ),
			// 'show_in_menu'  => 'edit.php?post_type=' . TM4MLP_TRANS_STATUS,
		)
	);
}

add_action( 'init', 'tm4mlp_cpt_cart' );

function tm4mlp_cart_taxonomy() {
	register_taxonomy(
		TM4MLP_TAX_PROJECT,
		TM4MLP_CART,
		array(
			'label'  => __( 'Project', 'tm4mlp' ),
			'public' => true,
		)
	);
}

add_action( 'init', 'tm4mlp_cart_taxonomy' );

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