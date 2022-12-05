<?php
/**
 * Hooks
 *
 * @since 1.0.0
 */

use function Translationmanager\Functions\get_supported_post_types;

// Projects Taxonomy.
add_action( 'translationmanager_project_pre_add_form', 'Translationmanager\\Functions\\project_hide_slug' );
add_action( 'translationmanager_project_pre_edit_form', 'Translationmanager\\Functions\\project_hide_slug' );

add_action('init', function () {
    foreach (get_supported_post_types() as $postTypeName) {
        add_filter("handle_bulk_actions-edit-{$postTypeName}", 'Translationmanager\\Functions\\bulk_translate_projects_by_request_posts', 10, 3 );
    }
}, 11);

// Misc.
add_action(
	'all_admin_notices',
	function () {

		\Translationmanager\Notice\TransientNoticeService::show();
	}
);

add_filter(
	'plugin_row_meta',
	function ( array $links, $file ) {

		static $plugin = null;

		// Avoid to create the same instance multiple times.
		// The action is performed for every plugin in the list.
		if ( null === $plugin ) {
			$plugin = new \Translationmanager\Plugin();
		}

		if ( false !== strpos( $file, 'translationmanager.php' ) ) {
			$links[1] = wp_kses(
				__(
					'By <a href="https://eurotext.de/en">Eurotext AG</a> & <a href="https://inpsyde.com/">Inpsyde GmbH</a>',
					'translationmanager'
				),
				'data'
			);
		}

		return $links;
	},
	10,
	2
);
add_filter(
	'admin_footer_text',
	function ( $admin_footer_text ) {

		$default_text = $admin_footer_text;
		$page         = sanitize_text_field( wp_unslash( $_GET['page'] ?? '' ) );

		if ( false !== strstr( $page, \Translationmanager\Pages\PageOptions::SLUG ) ) {
			$admin_footer_text = '<a href="http://inpsyde.com" class="inpsyde-logo-translationmanager" title="Inpsyde GmbH" class="screen-reader-text">Inpsyde GmbH</a></br>'
							 . $default_text;
		}

		return $admin_footer_text;
	}
);
