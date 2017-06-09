<?php

function tmwp_cart_taxonomy() {
	register_taxonomy(
		TMWP_TAX_PROJECT,
		TMWP_CART,
		array(
			'label'  => __( 'Projects', 'tmwp' ),
			'labels' => array(
				'add_new_item' => __( 'Create new project', 'tmwp' )
			),
			'public' => true,
		)
	);
}

add_action( 'init', 'tmwp_cart_taxonomy' );

function _tmwp_project_delete_coalesce( $term_id ) {
	$term = get_term( $term_id );

	if ( is_wp_error( $term )
	     || TMWP_TAX_PROJECT != $term->taxonomy
	) {
		return;
	}

	$posts = get_posts(
		array(
			'post_type'      => TMWP_CART,
			'post_status'    => 'any',
			'posts_per_page' => - 1,
			'tax_query'      => array(
				'taxonomy' => TMWP_TAX_PROJECT,
				'field'    => 'id',
				'terms'    => $term_id
			)
		)
	);

	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
}

add_action( 'delete_term_taxonomy', '_tmwp_project_delete_coalesce' );

function _tmwp_project_hide_slug() {
	?> <style> .form-field.term-slug-wrap, input[name=slug], span.title { display: none; } </style> <?php
}

add_action( TMWP_TAX_PROJECT . '_pre_add_form', '_tmwp_project_hide_slug' );
add_action( TMWP_TAX_PROJECT . '_pre_edit_form', '_tmwp_project_hide_slug' );