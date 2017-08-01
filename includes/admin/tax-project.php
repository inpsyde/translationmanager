<?php

function tmanager_cart_taxonomy() {
	register_taxonomy(
		TRANSLATIONMANAGER_TAX_PROJECT,
		TMANAGER_CART,
		array(
			'label'  => __( 'Projects', 'translationmanager' ),
			'labels' => array(
				'add_new_item' => __( 'Create new project', 'translationmanager' )
			),
			'public' => true,
		)
	);
}

add_action( 'init', 'tmanager_cart_taxonomy' );

function _translationmanager_project_delete_coalesce( $term_id ) {
	$term = get_term( $term_id );

	if ( is_wp_error( $term )
	     || TRANSLATIONMANAGER_TAX_PROJECT != $term->taxonomy
	) {
		return;
	}

	$posts = get_posts(
		array(
			'post_type'      => TMANAGER_CART,
			'post_status'    => 'any',
			'posts_per_page' => - 1,
			'tax_query'      => array(
				'taxonomy' => TRANSLATIONMANAGER_TAX_PROJECT,
				'field'    => 'id',
				'terms'    => $term_id
			)
		)
	);

	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
}

add_action( 'delete_term_taxonomy', '_translationmanager_project_delete_coalesce' );

function _translationmanager_project_hide_slug() {
	?> <style> .form-field.term-slug-wrap, input[name=slug], span.title { display: none; } </style> <?php
}

add_action( TRANSLATIONMANAGER_TAX_PROJECT . '_pre_add_form', '_translationmanager_project_hide_slug' );
add_action( TRANSLATIONMANAGER_TAX_PROJECT . '_pre_edit_form', '_translationmanager_project_hide_slug' );