<?php

/**
 * @param array    $data
 * @param \WP_Post $original_post
 *
 * @return array
 */
function tm4mlp_sanitize_post( $data, $original_post ) {
	$post_data = $data;

	unset( $post_data['post_author'] );
	unset( $post_data['post_date'] );
	unset( $post_data['post_date_gmt'] );
	unset( $post_data['post_status'] );
	unset( $post_data['comment_status'] );
	unset( $post_data['ping_status'] );
	unset( $post_data['post_password'] );
	unset( $post_data['to_ping'] );
	unset( $post_data['pinged'] );
	unset( $post_data['post_modified'] );
	unset( $post_data['post_modified_gmt'] );
	unset( $post_data['post_parent'] );
	unset( $post_data['menu_order'] );
	unset( $post_data['post_type'] );
	unset( $post_data['post_mime_type'] );
	unset( $post_data['comment_count'] );
	unset( $post_data['filter'] );
	unset( $post_data['ancestors'] );
	unset( $post_data['page_template'] );
	unset( $post_data['post_category'] );
	unset( $post_data['tags_input'] );
	unset( $post_data['post_content_filtered'] );

	if (!isset($post_data['__meta'])) {
		$post_data['__meta'] = array();
	}

	$post_data['__meta']['ID'] = $post_data['ID'];
	unset($post_data['ID']);

	$post_data['__meta']['guid'] = $post_data['guid'];
	unset($post_data['guid']);

	$data[ $original_post->post_type ] = $post_data;

	return $data;
}

add_filter( TM4MLP_SANITIZE_POST, 'tm4mlp_sanitize_post', 10, 2 );