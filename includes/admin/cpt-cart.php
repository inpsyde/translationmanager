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

function tm4mlp_bulk_actions( $actions ) {
	unset( $actions['edit'] );

	if ( isset( $actions['trash'] ) ) {
		$actions['trash'] = __( 'Remove from cart', 'tm4mlp' );
	}

	return $actions;
}

add_filter( 'bulk_actions-edit-' . TM4MLP_CART, 'tm4mlp_bulk_actions' );

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

	return array();
}

add_filter( 'post_row_actions', 'tm4mlp_cart_row_actions', 10, 2 );

function tm4mlp_cart_footer() {

}

add_action( 'admin_footer', 'tm4mlp_cart_footer' );

add_filter('views_edit-' . TM4MLP_CART,'my_filter');

function my_filter($views){
	$views['import'] = '<a href="#" class="primary">Import</a>';
	return $views;
}