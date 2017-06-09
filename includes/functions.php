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

	if( 'edit-tags' == $screen->base
	    && isset($_GET['taxonomy'])
	    && 'tm4mlp_project' == $_GET['taxonomy']
	    && isset($_GET['post_type'])
	    && 'tm4mlp_cart' == $_GET['post_type']
	) {
		echo '
			<style type="text/css">
				.post-type-tm4mlp_cart .row-actions span.edit, 
				.post-type-tm4mlp_cart .row-actions span.inline.hide-if-no-js, 
				.post-type-tm4mlp_cart .row-actions span.view {display: none !important;}
			</style>
			';
	}
}


add_action('admin_menu', 'inpsyde_tm4mlp_settings_menu_item');

/**
 * add external link to Translation area
 */
function inpsyde_tm4mlp_settings_menu_item() {
	global $submenu;
	$url = 'options-general.php?page=tm4mlp';
	$submenu['edit.php?post_type=tm4mlp_cart'][] = array('Settings', 'manage_options', $url);
}

add_action('admin_menu', 'inpsyde_tm4mlp_about_page');
/**
 * Adds a submenu page under a custom post type parent.
 */
function inpsyde_tm4mlp_about_page() {
	add_submenu_page(
		'edit.php?post_type=tm4mlp_cart',
		__( 'About', 'tm4mlp_cart' ),
		__( 'About', 'tm4mlp_cart' ),
		'manage_options',
		'inpsyde-tm4mlp-about',
		'inpsyde_tm4mlp_about_page_callback'
	);
}

/**
 * Display callback for the submenu page.
 */
function inpsyde_tm4mlp_about_page_callback() {
	?>
	<div class="wrap">
		Here any kinda text will go.
	</div>
	<?php
}

add_filter( 'plugin_row_meta', 'custom_plugin_row_meta', 10, 2 );
function custom_plugin_row_meta( $links, $file ) {
	if ( strpos( $file, TM4MLP_FILENAME ) !== false ) {
		$links[1] = 'By <a href="https://eurotext.de/">Eurotext AG</a> & <a href="https://inpsyde.com/">Inpsyde GmbH</a>';
	}
	return $links;
}