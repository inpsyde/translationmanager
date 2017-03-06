<?php

function tm4mlp_cart_taxonomy() {
	register_taxonomy(
		TM4MLP_TAX_PROJECT,
		TM4MLP_CART,
		array(
			'label'  => __( 'Projects', 'tm4mlp' ),
			'labels' => array(
				'add_new_item' => __( 'Create new project', 'tm4mlp' )
			),
			'public' => true,
		)
	);
}

add_action( 'init', 'tm4mlp_cart_taxonomy' );

function _tm4mlp_project_delete_coalesce( $term_id ) {
	$term = get_term( $term_id );

	if ( is_wp_error( $term )
	     || TM4MLP_TAX_PROJECT != $term->taxonomy
	) {
		return;
	}

	$posts = get_posts(
		array(
			'post_type'      => TM4MLP_CART,
			'post_status'    => 'any',
			'posts_per_page' => - 1,
			'tax_query'      => array(
				'taxonomy' => TM4MLP_TAX_PROJECT,
				'field'    => 'id',
				'terms'    => $term_id
			)
		)
	);

	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
}

add_action( 'delete_term_taxonomy', '_tm4mlp_project_delete_coalesce' );

function _tm4mlp_project_hide_slug() {
	?> <style> .form-field.term-slug-wrap { display: none; } </style> <?php
}

add_action( TM4MLP_TAX_PROJECT . '_pre_add_form', '_tm4mlp_project_hide_slug' );
add_action( TM4MLP_TAX_PROJECT . '_pre_edit_form', '_tm4mlp_project_hide_slug' );