<?php
/**
 * Hooks
 *
 * @since 1.0.0
 */

// CPT Project.
add_action( 'init', 'Translationmanager\\Functions\\register_translationmanager_project_posttype' );
add_action( 'admin_head', 'Translationmanager\\Functions\\project_remove_month' );
add_action( 'admin_init', [ \Translationmanager\PostType\ProjectItem::class, 'register_post_status' ] );
add_action( 'delete_term_taxonomy', 'Translationmanager\\Functions\\delete_all_projects_posts_based_on_project_taxonomy_term' );
add_action( 'admin_menu', function () {

	add_submenu_page(
		'translationmanager',
		esc_html__( 'Projects', 'translationmanager' ),
		esc_html__( 'Projects', 'translationmanager' ),
		'manage_options',
		'translationmanager-projects',
		'__return_false'
	);
} );
add_action( 'admin_menu', function () {

	global $submenu;

	$submenu['translationmanager'][0][2] = admin_url( 'edit-tags.php?taxonomy=translationmanager_project&post_type=project_item' );
} );

add_filter( 'bulk_actions-edit-project_item', 'Translationmanager\\Functions\\filter_bulk_actions_labels_for_project' );
add_filter( 'post_row_actions', 'Translationmanager\\Functions\\filter_row_actions_for_project', 10, 2 );
add_filter( 'display_post_states', 'Translationmanager\\Functions\\remove_states_from_project', 10, 2 );
add_filter( 'views_edit-project_item', 'Translationmanager\\Functions\\template_project_box_form_in_edit_page' );
add_filter( 'views_edit-project_item', 'Translationmanager\\Functions\\template_project_title_description_form_in_edit_page' );
add_filter( 'manage_project_item_posts_columns', [
	\Translationmanager\PostType\ProjectItem::class,
	'modify_columns',
] );
add_filter( 'manage_edit-translationmanager_project_columns', [
	\Translationmanager\Taxonomy\Project::class,
	'modify_columns',
] );
add_filter( 'translationmanager_project_row_actions', [
	\Translationmanager\Taxonomy\Project::class,
	'modify_row_actions',
], 10, 2 );
add_filter( 'bulk_post_updated_messages', 'Translationmanager\\Functions\\filter_bulk_updated_messages_for_project', 10, 2 );

// CPT Order.
add_action( 'init', 'Translationmanager\\Functions\\register_translationmanager_order_posttype' );
add_action( 'admin_head', 'Translationmanager\\Functions\\order_remove_month' );
add_action( 'trashed_post', 'Translationmanager\\Functions\\delete_post_order_on_trashing' );

// Projects Taxonomy.
add_action( 'init', 'Translationmanager\\Functions\\register_projects_taxonomy' );
add_action( 'translationmanager_project_pre_add_form', 'Translationmanager\\Functions\\project_hide_slug' );
add_action( 'translationmanager_project_pre_edit_form', 'Translationmanager\\Functions\\project_hide_slug' );
add_action( 'admin_post_translationmanager_project_info_save', 'Translationmanager\\Functions\\project_info_save' );

add_filter( 'bulk_actions-edit-tm_order', 'Translationmanager\\Functions\\filter_bulk_actions_for_order' );
add_filter( 'post_row_actions', 'Translationmanager\\Functions\\filter_row_actions_for_order', 10, 2 );
add_filter( 'get_edit_term_link', 'Translationmanager\\Functions\\edit_term_link_for_project_taxonomy', 10, 3 );
add_filter( 'handle_bulk_actions-edit-post', 'Translationmanager\\Functions\\bulk_translate_projects_by_request_posts', 10, 3 );
add_filter( 'handle_bulk_actions-edit-page', 'Translationmanager\\Functions\\bulk_translate_projects_by_request_posts', 10, 3 );

// Misc.
add_action( 'admin_head', function () {

	$screen = get_current_screen();
	$input  = (object) [
		'taxonomy'                   => filter_input( INPUT_GET, 'taxonomy', FILTER_SANITIZE_STRING ),
		'post_type'                  => filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_STRING ),
		'translationmanager_project' => filter_input( INPUT_GET, 'translationmanager_project', FILTER_SANITIZE_STRING ),
	];

	if ( 'edit' === $screen->base
	     && $input->translationmanager_project
	     && 'project_item' === $input->post_type
	) {
		echo '<style type="text/css">.post-type-project_item #posts-filter .search-box {display: none !important;}</style>';
	}

	if ( 'edit-tags' === $screen->base
	     && 'translationmanager_project' === $input->taxonomy
	     && 'project_item' === $input->post_type
	) {
		echo '
			<style type="text/css">
				.post-type-project_item .row-actions span.edit, 
				.post-type-project_item .row-actions span.inline.hide-if-no-js, 
				.post-type-project_item .row-actions span.view {display: none !important;}
			</style>
			';
	}
} );
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

	if ( false !== strstr( $page, 'inpsyde-translationmanager-about' ) ) {
		$admin_footer_text = '<a href="http://inpsyde.com" class="inpsyde_logo_translationmanager" title="Inpsyde GmbH">Inpsyde GmbH</a></br>'
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