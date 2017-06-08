<?php

add_action( 'admin_head', 'inpsyde_remove_search_box' );
function inpsyde_remove_search_box() {
	$screen = get_current_screen();
	if( 'edit' == $screen->base
	    && isset($_GET['tm4mlp_project'])
	    && isset($_GET['post_type'])
	    && 'tm4mlp_cart' == $_GET['post_type']
	) {
		echo '<style type="text/css">.post-type-tm4mlp_cart #posts-filter .search-box {display: none !important;}</style>';
	}
}