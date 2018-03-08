<?php
/**
 * Hooks
 *
 * @since 1.0.0
 */

// CPT Project.
add_action( 'delete_term_taxonomy', 'Translationmanager\\Functions\\delete_all_projects_posts_based_on_project_taxonomy_term' );

// CPT Order.
add_action( 'init', 'Translationmanager\\Functions\\register_translationmanager_order_posttype' );
add_action( 'admin_head', 'Translationmanager\\Functions\\order_remove_month' );
add_action( 'trashed_post', 'Translationmanager\\Functions\\delete_post_order_on_trashing' );

// Projects Taxonomy.
add_action( 'translationmanager_project_pre_add_form', 'Translationmanager\\Functions\\project_hide_slug' );
add_action( 'translationmanager_project_pre_edit_form', 'Translationmanager\\Functions\\project_hide_slug' );

add_filter( 'bulk_actions-edit-tm_order', 'Translationmanager\\Functions\\filter_bulk_actions_for_order' );
add_filter( 'post_row_actions', 'Translationmanager\\Functions\\filter_row_actions_for_order', 10, 2 );
add_filter( 'get_edit_term_link', 'Translationmanager\\Functions\\edit_term_link_for_project_taxonomy', 10, 3 );
add_filter( 'handle_bulk_actions-edit-post', 'Translationmanager\\Functions\\bulk_translate_projects_by_request_posts', 10, 3 );
add_filter( 'handle_bulk_actions-edit-page', 'Translationmanager\\Functions\\bulk_translate_projects_by_request_posts', 10, 3 );

// Misc.
add_action( 'all_admin_notices', function () {

	\Translationmanager\Notice\TransientNoticeService::show();
} );

add_filter( 'plugin_row_meta', function ( array $links, $file ) {

	static $plugin = null;

	// Avoid to create the same instance multiple times.
	// The action is performed for every plugin in the list.
	if ( null === $plugin ) {
		$plugin = new \Translationmanager\Plugin();
	}

	if ( false !== strpos( $file, 'translationmanager.php' ) ) {
		$links[1] = strip_tags( __(
			'By <a href="https://eurotext.de/en">Eurotext AG</a> & <a href="https://inpsyde.com/">Inpsyde GmbH</a>',
			'translationmanager'
		), '<a>' );
	}

	return $links;
}, 10, 2 );
add_filter( 'admin_footer_text', function ( $admin_footer_text ) {

	$default_text = $admin_footer_text;
	$page         = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

	if ( false !== strstr( $page, \Translationmanager\Pages\PageOptions::SLUG ) ) {
		$admin_footer_text = '<a href="http://inpsyde.com" class="inpsyde_logo_translationmanager" title="Inpsyde GmbH" class="screen-reader-text">Inpsyde GmbH</a></br>'
		                     . $default_text;
	}

	return $admin_footer_text;
} );
add_filter( 'bulk_actions-edit-post', function ( $actions ) {

	$actions['bulk_translate'] = esc_html__( 'Bulk Translate', 'translationmanager' );

	return $actions;
} );
add_filter( 'bulk_actions-edit-page', function ( $actions ) {

	$actions['bulk_translate'] = esc_html__( 'Bulk Translate', 'translationmanager' );

	return $actions;
} );