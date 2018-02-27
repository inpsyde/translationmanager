<?php

function translationmanager_cpt_order() {
	register_post_type(
		'tm_order',
		array(
			'capabilities' => array(
				// Removes support for the "Add New" function ( use 'do_not_allow' / false for multisite set ups ).
				'create_posts' => false,
			),
			'description'  => esc_html__( 'Your placed orders.', 'translationmanager' ),
			'hierarchical' => true,
			'label'        => esc_html__( 'Order', 'translationmanager' ),
			'map_meta_cap' => true,
			'public'       => true,
			'show_in_menu' => 'edit.php?post_type=translationmanager_trans_status',
			'supports'     => array( 'title' ),
		)
	);
}

add_action( 'init', 'translationmanager_cpt_order' );

function tmanager_order_clean() {
	// Remove all WordPress basics as this post type is not meant to be maintained by users.
	remove_meta_box( 'submitdiv', 'translationmanager_order', 'side' );
	remove_meta_box( 'slugdiv', 'translationmanager_order', 'normal' );

	$screen = get_current_screen();

	if ( 'tm_order' !== $screen->post_type ) {
		return;
	}

	add_filter( 'months_dropdown_results', '__return_empty_array' );
}

add_action( 'admin_head', 'tmanager_order_clean' );

function translationmanager_bulk_actions_order( $actions ) {
	unset( $actions['edit'] );
	unset( $actions['trash'] );

	return $actions;
}

add_filter( 'bulk_actions-edit-tm_order', 'translationmanager_bulk_actions_order' );

/**
 * Order items have no trash.
 *
 * @param $post_id
 */
function tmanager_order_trashed( $post_id ) {
	$post_type = get_post_type($post_id);

	if ( 'tm_order' !== $post_type ) {
		return;
	}

	wp_delete_post( $post_id );
}

add_action( 'trashed_post', 'tm_order_trashed' );

/**
 * @param          $actions
 * @param \WP_Post $post
 */
function tmanager_order_row_actions( $actions, $post ) {

	if ( $post && 'tm_order' !== $post->post_type ) {
		return $actions;
	}

	return array('a' => 'b');
}

add_filter( 'post_row_actions', 'tmanager_order_row_actions', 10, 2 );


function tmanager_order_info() {
	static $order_info;

	if ( ! $order_info ) {
		$order_info = new \Translationmanager\Meta_Box\Order_Info();
	}

	$order_info->add_meta_box();
}

add_action( 'add_meta_boxes', 'tmanager_order_info' );
