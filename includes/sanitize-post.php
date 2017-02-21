<?php

function tm4mlp_sanitize_post( $data, $original_post ) {
	unset( $data['ID'] );
	unset( $data['post_author'] );
	unset( $data['post_date'] );
	unset( $data['post_date_gmt'] );
	unset( $data['post_status'] );
	unset( $data['comment_status'] );
	unset( $data['ping_status'] );
	unset( $data['post_password'] );
	unset( $data['to_ping'] );
	unset( $data['pinged'] );
	unset( $data['post_modified'] );
	unset( $data['post_modified_gmt'] );
	unset( $data['post_parent'] );
	unset( $data['menu_order'] );
	unset( $data['post_type'] );
	unset( $data['post_mime_type'] );
	unset( $data['comment_count'] );
	unset( $data['filter'] );
	unset( $data['ancestors'] );
	unset( $data['page_template'] );
	unset( $data['post_category'] );
	unset( $data['tags_input'] );

	return $data;
}

add_filter( 'tm4mlp_sanitize_post', 'tm4mlp_sanitize_post', 10, 2 );