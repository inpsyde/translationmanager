<?php
/**
 * Hooks
 *
 * @since 1.0.0
 */

// CPT Project.
add_action( 'delete_term_taxonomy', 'Translationmanager\\Functions\\delete_all_projects_posts_based_on_project_taxonomy_term' );

// Projects Taxonomy.
add_action( 'translationmanager_project_pre_add_form', 'Translationmanager\\Functions\\project_hide_slug' );
add_action( 'translationmanager_project_pre_edit_form', 'Translationmanager\\Functions\\project_hide_slug' );

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
		$admin_footer_text = '<a href="http://inpsyde.com" class="inpsyde-logo-translationmanager" title="Inpsyde GmbH" class="screen-reader-text">Inpsyde GmbH</a></br>'
		                     . $default_text;
	}

	return $admin_footer_text;
} );
