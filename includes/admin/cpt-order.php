<?php

function tm4mlp_cpt_order() {
	register_post_type(
		TM4MLP_ORDER,
		array(
			'capabilities' => array(
				// Removes support for the "Add New" function ( use 'do_not_allow' / false for multisite set ups ).
				'create_posts' => false,
			),
			'description'  => __( 'Your placed orders.', 'translationmanager' ),
			'hierarchical' => true,
			'label'        => __( 'Order', 'translationmanager' ),
			'map_meta_cap' => true,
			'public'       => true,
			'show_in_menu' => 'edit.php?post_type=' . TM4MLP_TRANS_STATUS,
			'supports'     => array( 'title' ),
		)
	);
}

add_action( 'init', 'tm4mlp_cpt_order' );

function tm4mlp_order_clean() {
	// Remove all WordPress basics as this post type is not meant to be maintained by users.
	remove_meta_box( 'submitdiv', TM4MLP_ORDER, 'side' );
	remove_meta_box( 'slugdiv', TM4MLP_ORDER, 'normal' );

	$screen = get_current_screen();

	if ( TM4MLP_ORDER != $screen->post_type ) {
		return;
	}

	add_filter( 'months_dropdown_results', '__return_empty_array' );
}

add_action( 'admin_head', 'tm4mlp_order_clean' );

function tm4mlp_bulk_actions_order( $actions ) {
	unset( $actions['edit'] );
	unset( $actions['trash'] );

	return $actions;
}

add_filter( 'bulk_actions-edit-' . TM4MLP_ORDER, 'tm4mlp_bulk_actions_order' );

/**
 * Order items have no trash.
 *
 * @param $post_id
 */
function tm4mlp_order_trashed( $post_id ) {
	$post_type = get_post_type($post_id);

	if ( TM4MLP_ORDER != $post_type ) {
		return;
	}

	wp_delete_post( $post_id );
}

add_action( 'trashed_post', 'tm4mlp_order_trashed' );

/**
 * @param          $actions
 * @param \WP_Post $post
 */
function tm4mlp_order_row_actions( $actions, $post ) {

	if ( $post && TM4MLP_ORDER != $post->post_type ) {
		return $actions;
	}

	return array('a' => 'b');
}

add_filter( 'post_row_actions', 'tm4mlp_order_row_actions', 10, 2 );


function tm4mlp_order_info() {
	static $order_info;

	if ( ! $order_info ) {
		$order_info = new \Tm4mlp\Meta_Box\Order_Info();
	}

	$order_info->add_meta_box();
}

add_action( 'add_meta_boxes', 'tm4mlp_order_info' );
