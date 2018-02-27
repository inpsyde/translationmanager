<?php

function tmanager_cart_taxonomy() {
	register_taxonomy(
		'translationmanager_project',
		'tmanager_cart',
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
	     || 'translationmanager_project' !== $term->taxonomy
	) {
		return;
	}

	$posts = get_posts(
		array(
			'post_type'      => 'tmanager_cart',
			'post_status'    => 'any',
			'posts_per_page' => - 1,
			'tax_query'      => array(
				'taxonomy' => 'translationmanager_project',
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

add_action( 'translationmanager_project_pre_add_form', '_translationmanager_project_hide_slug' );
add_action( 'translationmanager_project_pre_edit_form', '_translationmanager_project_hide_slug' );